<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Hudsyn\SocialPost;
use App\Hudsyn\TinyUrl;
use App\Hudsyn\Setting;
use App\Hudsyn\Blog;
use App\Hudsyn\PressRelease;
use Exception;
use Illuminate\Support\Facades\Http;

class SocialController extends Controller
{
    /**
     * Display a listing of scheduled or sent social posts.
     */
    public function index()
    {
        $posts = SocialPost::orderBy('scheduled_for', 'desc')->get();
        return view('hudsyn.social.index', compact('posts'));
    }

    /**
     * Show the form for creating a new social post.
     */
    public function create()
    {
        // Fetch timezone from settings, default to America/Los_Angeles
        $timezoneSetting = Setting::where('key', 'system_timezone')->first();
        $timezone = $timezoneSetting ? $timezoneSetting->value : 'America/Los_Angeles';

        // Get current time in that timezone
        $currentTime = now()->setTimezone($timezone)->format('Y-m-d H:i');

        // Fetch blog posts and press releases
        $blogs = Blog::select('id', 'title')->get();
        $pressReleases = PressRelease::select('id', 'title')->get();

        // Combine into a single collection
        $contents = collect([]);
        foreach ($blogs as $blog) {
            $contents->push((object)['id' => $blog->id, 'title' => $blog->title, 'type' => 'blog']);
        }
        foreach ($pressReleases as $press) {
            $contents->push((object)['id' => $press->id, 'title' => $press->title, 'type' => 'press']);
        }

        return view('hudsyn.social.create', compact('contents', 'timezone', 'currentTime'));
    }

    /**
     * Store a newly created social post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text_content'  => 'nullable|string',
            'scheduled_for' => 'nullable|date',
            'post_now'      => 'nullable|in:0,1',
            'platforms'     => 'nullable|array',
            'content_type'  => 'nullable|in:blog,press_release',
            'content_id'    => 'nullable|integer',
            'image'         => 'nullable|image|max:10240' // 10MB max
        ]);

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $uploadDir = 'uploads/social';

            if (!file_exists(public_path($uploadDir))) {
                mkdir(public_path($uploadDir), 0755, true);
            }

            $file->move(public_path($uploadDir), $imageName);
            $imagePath = $uploadDir . '/' . $imageName;
        }

        // Generate a tiny URL if a blog post or press release is selected
        $tinyUrlId = null;
        if (!empty($validated['content_type']) && !empty($validated['content_id'])) {
            $shortCode = Str::random(8);
            $originalUrl = route('hudsyn.public.' . $validated['content_type'], $validated['content_id']);
            
            $tinyUrl = TinyUrl::create([
                'short_code'   => $shortCode,
                'original_url' => $originalUrl,
            ]);
            
            $tinyUrlId = $tinyUrl->id;
        }

        // Get system timezone from settings, default to 'America/Los_Angeles'
        $timezoneSetting = Setting::where('key', 'system_timezone')->first();
        $timezone = $timezoneSetting ? $timezoneSetting->value : 'America/Los_Angeles';

        // Determine scheduled posting time
        if ($request->has('post_now')) {
            $scheduledTime = now()->setTimezone($timezone);
            $status = 'in_progress'; // Mark as in-progress immediately
        } else {
            $scheduledTime = Carbon::parse($validated['scheduled_for'])->setTimezone($timezone);
            $status = 'scheduled';
        }

        // Create the social post record
        $post = SocialPost::create([
            'text_content'  => $validated['text_content'] ?? null,
            'image_path'    => $imagePath,
            'content_type'  => $validated['content_type'] ?? null,
            'content_id'    => $validated['content_id'] ?? null,
            'tinyurl_id'    => $tinyUrlId,
            'scheduled_for' => $scheduledTime,
            'status'        => $status,
            'platforms'     => $validated['platforms'] ?? [],
        ]);

        // If post is set to "Post Now", immediately attempt to send it
        if ($request->has('post_now')) {
            $socialResults = $this->postToSocialMedia($post);
            $post->update([
                'platform_results' => $socialResults,
                'status'           => in_array('Success', $socialResults) ? 'complete' : 'failed'
            ]);
        }

        return redirect()->route('hudsyn.social.index')
                         ->with('success', 'Social post created successfully.');
    }

    /**
     * Show the form for editing the specified social post.
     */
    public function edit($id)
    {
        $post = SocialPost::findOrFail($id);

        // Fetch blog posts and press releases as selectable content
        $blogs = Blog::select('id', 'title')->get();
        $pressReleases = PressRelease::select('id', 'title')->get();

        // Combine them into a single collection
        $contents = collect([]);
        foreach ($blogs as $blog) {
            $contents->push((object)[
                'id'    => $blog->id,
                'title' => $blog->title,
                'type'  => 'blog'
            ]);
        }
        foreach ($pressReleases as $press) {
            $contents->push((object)[
                'id'    => $press->id,
                'title' => $press->title,
                'type'  => 'press'
            ]);
        }

        return view('hudsyn.social.edit', compact('post', 'contents'));
    }


    /**
     * Update the specified social post in storage.
     */
    public function update(Request $request, $id)
    {
        $post = SocialPost::findOrFail($id);
        $validated = $request->validate([
            'text_content'  => 'nullable|string',
            'scheduled_for' => 'required|date',
            'platforms'     => 'nullable|array',
        ]);

        $post->update([
            'text_content'  => $validated['text_content'],
            'scheduled_for' => Carbon::parse($validated['scheduled_for']),
            'platforms'     => $validated['platforms'] ?? [],
        ]);

        return redirect()->route('hudsyn.social.index')
                         ->with('success', 'Social post updated successfully.');
    }

    /**
     * Process scheduled social posts.
     * This method is triggered via a GET request to /hudsyn/cron.
     */
    public function processScheduledPosts()
    {
        // Get all posts that are scheduled and whose scheduled time is now or in the past.
        $posts = SocialPost::where('scheduled_for', '<=', Carbon::now())
            ->where('status', 'scheduled')
            ->get();

        foreach ($posts as $post) {
            // Update the post status to in_progress before processing
            $post->status = 'in_progress';
            $post->save();

            // Attempt to post to each selected platform and collect results
            $results = $this->postToSocialMedia($post);

            // Log the results and update status accordingly.
            $post->platform_results = $results;

            // If all platforms report success, mark as complete; otherwise, mark as failed.
            $allSuccessful = true;
            foreach ($results as $platform => $result) {
                if (strpos($result, 'Failed') !== false) {
                    $allSuccessful = false;
                    break;
                }
            }
            $post->status = $allSuccessful ? 'complete' : 'failed';
            $post->save();
        }

        return response()->json([
            'message' => 'Processed ' . count($posts) . ' social post(s).',
        ]);
    }

    /**
     * Post the given social post to the selected social media platforms.
     *
     * @param  \Jopanel\Hudsyn\Models\SocialPost  $post
     * @return array  Array of results per platform.
     */
    protected function postToSocialMedia($post)
    {
        $results = [];
        $platforms = is_array($post->platforms) ? $post->platforms : [];

        foreach ($platforms as $platform) {
            try {
                // Retrieve API credentials from settings using a key pattern.
                $settingKey = 'social_' . $platform . '_api_key';
                $credential = Setting::where('key', $settingKey)->first();
                if (!$credential || empty($credential->value)) {
                    throw new \Exception("Missing API credential for {$platform}");
                }

                // Prepare the text for posting. Optionally append the tiny URL if exists.
                $text = $post->text_content;
                if (!empty($post->tinyurl_id)) {
                    // Optionally, append a tiny URL here. For example:
                    $tinyUrl = TinyUrl::find($post->tinyurl_id);
                    if ($tinyUrl) {
                         $text .= "\n" . route('hudsyn.tinyurl.redirect', $tinyUrl->short_code);
                     }
                }

                // Use platform-specific logic:
                switch ($platform) {
                    case 'x':  // Twitter (X)
                        // Retrieve Twitter credentials for token refresh.
                        $tokenSetting = Setting::where('key', 'social_x_user_token')->first();
                        $expiresSetting = Setting::where('key', 'social_x_user_token_expires')->first();
                        $refreshTokenSetting = Setting::where('key', 'social_x_user_refresh_token')->first();

                        if (!$tokenSetting || empty($tokenSetting->value)) {
                            throw new \Exception("Twitter posting is not configured. Please authenticate.");
                        }

                        // Check if the token has expired.
                        if ($expiresSetting && strtotime($expiresSetting->value) <= time()) {
                            if (!$refreshTokenSetting || empty($refreshTokenSetting->value)) {
                                throw new \Exception("Twitter token expired and refresh token is missing. Please re-authenticate.");
                            }

                            $credential = Setting::where('key', 'social_x_api_key')->first();
                            if (!$credential || empty($credential->value)) {
                                throw new \Exception("Missing API key for X");
                            } 
                            $clientId = $credential->value;
                            $credential2 = Setting::where('key', 'social_x_api_secret')->first();
                            if (!$credential2 || empty($credential2->value)) {
                                throw new \Exception("Missing API key for X");
                            } 
                            $clientSecret = $credential2->value;
                            
                            $refreshResponse = \Illuminate\Support\Facades\Http::asForm()->withBasicAuth($clientId, $clientSecret)
                                ->post('https://api.twitter.com/2/oauth2/token', [
                                    'grant_type'    => 'refresh_token',
                                    'refresh_token' => $refreshTokenSetting->value,
                                ]);
                            
                            if (!$refreshResponse->successful()) {
                                throw new \Exception("Twitter token refresh failed: " . $refreshResponse->body());
                            }
                            
                            $refreshData = $refreshResponse->json();
                            // Update settings with the new token details.
                            $tokenSetting->update(['value' => $refreshData['access_token']]);
                            $expiresSetting->update(['value' => date('c', time() + $refreshData['expires_in'])]);
                            $refreshTokenSetting->update(['value' => $refreshData['refresh_token']]);
                            
                            $twitterToken = $refreshData['access_token'];
                        } else {
                            $twitterToken = $tokenSetting->value;
                        }
                        
                        // Build payload for a text-only tweet.
                        $data = [
                            'text' => $text,
                        ];
                        
                        $endpoint = 'https://api.twitter.com/2/tweets';
                        
                        $response = \Illuminate\Support\Facades\Http::withToken($twitterToken)
                            ->acceptJson()
                            ->post($endpoint, $data);
                        
                        if (!$response->successful()) {
                            throw new \Exception("Twitter API error: " . $response->body());
                        }
                        
                        $results[$platform] = 'Success';
                        break;

                    case 'instagram':
                        // For Instagram, we need an Instagram Business Account ID (IG User ID)
                        $igUserIdSetting = Setting::where('key', 'social_instagram_ig_user_id')->first();
                        if (!$igUserIdSetting || empty($igUserIdSetting->value)) {
                            throw new \Exception("Missing Instagram IG User ID");
                        }
                        $igUserId = $igUserIdSetting->value;
                        
                        // Instagram requires an image; if no image is uploaded, throw an error.
                        if (!$post->image_path) {
                            throw new \Exception("Instagram posts require an image.");
                        }

                        // Step 1: Create media container
                        $dataForContainer = [
                            'image_url'    => asset($post->image_path),
                            'caption'      => $text,
                            'access_token' => $credential->value,
                        ];
                        $containerResponse = Http::post(
                            "https://graph.facebook.com/v17.0/{$igUserId}/media",
                            $dataForContainer
                        );
                        if (!$containerResponse->successful()) {
                            throw new \Exception("Instagram media container error: " . $containerResponse->body());
                        }
                        $containerId = $containerResponse->json()['id'] ?? null;
                        if (!$containerId) {
                            throw new \Exception("Failed to create Instagram media container.");
                        }

                        // Step 2: Publish the media container
                        $publishResponse = Http::post(
                            "https://graph.facebook.com/v17.0/{$igUserId}/media_publish",
                            [
                                'creation_id'  => $containerId,
                                'access_token' => $credential->value,
                            ]
                        );
                        if (!$publishResponse->successful()) {
                            throw new \Exception("Instagram media publish error: " . $publishResponse->body());
                        }
                        $results[$platform] = 'Success';
                    break;

                    case 'linkedin':
                        // Retrieve the LinkedIn API token (credential) as before.
                        // $credential is already retrieved for this platform.
                        // Retrieve the author URN from settings.
                        $authorSetting = Setting::where('key', 'social_linkedin_author_urn')->first();
                        if (!$authorSetting || empty($authorSetting->value)) {
                            throw new \Exception("Missing LinkedIn Author URN");
                        }
                        $authorUrn = $authorSetting->value;
                        
                        // Set the endpoint for LinkedIn UGC posts.
                        $endpoint = 'https://api.linkedin.com/v2/ugcPosts';
                        
                        // Build the payload for a text-only post.
                        $data = [
                            "author" => $authorUrn,
                            "lifecycleState" => "PUBLISHED",
                            "specificContent" => [
                                "com.linkedin.ugc.ShareContent" => [
                                    "shareCommentary" => [
                                        "text" => $text
                                    ],
                                    "shareMediaCategory" => "NONE"
                                ]
                            ],
                            "visibility" => [
                                "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC"
                            ]
                        ];
                        
                        // Use Laravel's HTTP client with the LinkedIn token.
                        $response = Http::withToken($credential->value)
                            ->withHeaders([
                                'X-Restli-Protocol-Version' => '2.0.0'
                            ])
                            ->post($endpoint, $data);
                        
                        if (!$response->successful()) {
                            throw new \Exception("LinkedIn API error: " . $response->body());
                        }
                        
                        $results[$platform] = 'Success';
                        break;


                    case 'facebook':
                        // Retrieve Facebook API credentials from settings.
                        $fbCredential = \Jopanel\Hudsyn\Models\Setting::where('key', 'social_facebook_api_key')->first();
                        if (!$fbCredential || empty($fbCredential->value)) {
                            throw new \Exception("Missing API credential for facebook");
                        }
                        
                        // Optionally, retrieve the Facebook Page ID from settings.
                        $fbPageIdSetting = \Jopanel\Hudsyn\Models\Setting::where('key', 'social_facebook_page_id')->first();
                        
                        // Prepare the text content (assume $text already includes any appended tiny URL).
                        // You may have prepared $text earlier in the method.
                        
                        $fbEndpoint = null;
                        $data = [];
                        
                        if ($post->image_path) {
                            // For posts with an image, post to /photos.
                            if ($fbPageIdSetting && !empty($fbPageIdSetting->value)) {
                                $fbEndpoint = "https://graph.facebook.com/v12.0/{$fbPageIdSetting->value}/photos";
                            } else {
                                $fbEndpoint = "https://graph.facebook.com/v12.0/me/photos";
                            }
                            
                            $data = [
                                'caption'       => $text,
                                'access_token'  => $fbCredential->value,
                            ];
                            
                            // Use Laravel's HTTP client to attach the image file.
                            $response = \Illuminate\Support\Facades\Http::attach(
                                    'source', 
                                    file_get_contents(public_path($post->image_path)), 
                                    basename($post->image_path)
                                )
                                ->post($fbEndpoint, $data);
                        } else {
                            // For text-only posts, post to /feed.
                            if ($fbPageIdSetting && !empty($fbPageIdSetting->value)) {
                                $fbEndpoint = "https://graph.facebook.com/v12.0/{$fbPageIdSetting->value}/feed";
                            } else {
                                $fbEndpoint = "https://graph.facebook.com/v12.0/me/feed";
                            }
                            
                            $data = [
                                'message'      => $text,
                                'access_token' => $fbCredential->value,
                            ];
                            
                            $response = \Illuminate\Support\Facades\Http::post($fbEndpoint, $data);
                        }
                        
                        if (!$response->successful()) {
                            throw new \Exception("Facebook API error: " . $response->body());
                        }
                        
                        $results[$platform] = 'Success';
                        break;

                    default:
                        throw new \Exception("Unsupported platform: {$platform}");
                }
            } catch (\Exception $e) {
                $results[$platform] = 'Failed: ' . $e->getMessage();
            }
        }

        return $results;
    }


    /**
     * Optional: Preview a social post before sending.
     */
    public function preview($id)
    {
        $post = SocialPost::findOrFail($id);
        return view('hudsyn.social.preview', compact('post'));
    }

    /**
     * Remove the specified social post from storage.
     */
    public function destroy($id)
    {
        $post = SocialPost::findOrFail($id);
        $post->delete();

        return redirect()->route('hudsyn.social.index')
                         ->with('success', 'Social post deleted successfully.');
    }

    public function twitterAuth()
    {
        // Generate a random state and code verifier for PKCE.
        $state = Str::random(16);
        $codeVerifier = Str::random(64);
        session([
            'twitter_oauth_state' => $state,
            'twitter_code_verifier' => $codeVerifier,
        ]);
        
        // Generate the code challenge from the verifier.
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
        
        // Callback URL must match the one set in your Twitter app.
        $redirectUri = route('hudsyn.twitter.callback');
        $scopes = 'tweet.write tweet.read'; // Adjust scopes as needed.
        
        $authUrl = 'https://twitter.com/i/oauth2/authorize?' . http_build_query([
             'response_type'       => 'code',
             'client_id'           => config('services.twitter.client_id', ''), // Or retrieve from settings
             'redirect_uri'        => $redirectUri,
             'scope'               => $scopes,
             'state'               => $state,
             'code_challenge'      => $codeChallenge,
             'code_challenge_method' => 'S256',
        ]);
        
        return redirect($authUrl);
    }

    public function twitterCallback(Request $request)
    {
        // Verify state matches.
        if ($request->input('state') !== session('twitter_oauth_state')) {
            return redirect()->route('hudsyn.social.index')->withErrors('Invalid state in Twitter callback.');
        }
        
        $code = $request->input('code');
        $codeVerifier = session('twitter_code_verifier'); 
        $credential = Setting::where('key', 'social_x_api_key')->first();
        if (!$credential || empty($credential->value)) {
            throw new \Exception("Missing API key for X");
        } 
        $clientId = $credential->value;
        $credential2 = Setting::where('key', 'social_x_api_secret')->first();
        if (!$credential2 || empty($credential2->value)) {
            throw new \Exception("Missing API key for X");
        } 
        $clientSecret = $credential2->value;
        $redirectUri = route('hudsyn.twitter.callback');

        $tokenResponse = Http::asForm()->withBasicAuth($clientId, $clientSecret)
            ->post('https://api.twitter.com/2/oauth2/token', [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $redirectUri,
                'code_verifier' => $codeVerifier,
            ]);

        if (!$tokenResponse->successful()) {
            return redirect()->route('hudsyn.social.index')->withErrors('Twitter token exchange failed: ' . $tokenResponse->body());
        }

        $tokenData = $tokenResponse->json();
        // Store token details in settings for future use.
        Setting::updateOrCreate(
            ['key' => 'social_x_user_token'],
            ['value' => $tokenData['access_token']]
        );
        Setting::updateOrCreate(
            ['key' => 'social_x_user_token_expires'],
            ['value' => date('c', time() + $tokenData['expires_in'])]
        );
        Setting::updateOrCreate(
            ['key' => 'social_x_user_refresh_token'],
            ['value' => $tokenData['refresh_token']]
        );

        return redirect()->route('hudsyn.social.index')
                         ->with('success', 'Twitter authenticated successfully.');
    }

    /**
     * Initiate LinkedIn OAuth flow
     */
    public function linkedinAuth()
    {
        $state = Str::random(16);
        session(['linkedin_oauth_state' => $state]);
        
        $redirectUri = route('hudsyn.linkedin.callback');
        $scopes = 'r_liteprofile r_emailaddress w_member_social';
        
        $authUrl = 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id'     => config('services.linkedin.client_id'),
            'redirect_uri'  => $redirectUri,
            'scope'         => $scopes,
            'state'         => $state,
        ]);
        
        return redirect($authUrl);
    }

    /**
     * Handle LinkedIn OAuth callback
     */
    public function linkedinCallback(Request $request)
    {
        if ($request->input('state') !== session('linkedin_oauth_state')) {
            return redirect()->route('hudsyn.settings.index')->withErrors('Invalid state in LinkedIn callback.');
        }

        $code = $request->input('code');
        $redirectUri = route('hudsyn.linkedin.callback');

        $tokenResponse = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirectUri,
            'client_id'     => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        if (!$tokenResponse->successful()) {
            return redirect()->route('hudsyn.settings.index')
                           ->withErrors('LinkedIn token exchange failed: ' . $tokenResponse->body());
        }

        $tokenData = $tokenResponse->json();
        
        // Get user profile to get the URN
        $profileResponse = Http::withToken($tokenData['access_token'])
            ->get('https://api.linkedin.com/v2/me');
            
        if (!$profileResponse->successful()) {
            return redirect()->route('hudsyn.settings.index')
                           ->withErrors('Failed to get LinkedIn profile: ' . $profileResponse->body());
        }

        $profileData = $profileResponse->json();
        
        // Store token and URN in settings
        Setting::updateOrCreate(
            ['key' => 'social_linkedin_user_token'],
            ['value' => $tokenData['access_token']]
        );
        Setting::updateOrCreate(
            ['key' => 'social_linkedin_user_token_expires'],
            ['value' => date('c', time() + $tokenData['expires_in'])]
        );
        Setting::updateOrCreate(
            ['key' => 'social_linkedin_author_urn'],
            ['value' => $profileData['id']]
        );

        return redirect()->route('hudsyn.settings.index')
                       ->with('success', 'LinkedIn authenticated successfully.');
    }

    /**
     * Initiate Facebook OAuth flow
     */
    public function facebookAuth()
    {
        $state = Str::random(16);
        session(['facebook_oauth_state' => $state]);
        
        $redirectUri = route('hudsyn.facebook.callback');
        $scopes = 'pages_manage_posts,pages_read_engagement,instagram_basic,instagram_content_publish';
        
        $authUrl = 'https://www.facebook.com/v17.0/dialog/oauth?' . http_build_query([
            'client_id'     => config('services.facebook.client_id'),
            'redirect_uri'  => $redirectUri,
            'scope'         => $scopes,
            'state'         => $state,
            'response_type' => 'code',
        ]);
        
        return redirect($authUrl);
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function facebookCallback(Request $request)
    {
        if ($request->input('state') !== session('facebook_oauth_state')) {
            return redirect()->route('hudsyn.settings.index')
                           ->withErrors('Invalid state in Facebook callback.');
        }

        $code = $request->input('code');
        $redirectUri = route('hudsyn.facebook.callback');

        $tokenResponse = Http::get('https://graph.facebook.com/v17.0/oauth/access_token', [
            'client_id'     => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri'  => $redirectUri,
            'code'          => $code,
        ]);

        if (!$tokenResponse->successful()) {
            return redirect()->route('hudsyn.settings.index')
                           ->withErrors('Facebook token exchange failed: ' . $tokenResponse->body());
        }

        $tokenData = $tokenResponse->json();
        
        // Get user's pages
        $pagesResponse = Http::withToken($tokenData['access_token'])
            ->get('https://graph.facebook.com/v17.0/me/accounts');
            
        if (!$pagesResponse->successful()) {
            return redirect()->route('hudsyn.settings.index')
                           ->withErrors('Failed to get Facebook pages: ' . $pagesResponse->body());
        }

        $pagesData = $pagesResponse->json();
        
        // Store token and page ID in settings
        Setting::updateOrCreate(
            ['key' => 'social_facebook_user_token'],
            ['value' => $tokenData['access_token']]
        );
        Setting::updateOrCreate(
            ['key' => 'social_facebook_user_token_expires'],
            ['value' => date('c', time() + $tokenData['expires_in'])]
        );
        
        // If there are pages, store the first page ID
        if (!empty($pagesData['data'])) {
            Setting::updateOrCreate(
                ['key' => 'social_facebook_page_id'],
                ['value' => $pagesData['data'][0]['id']]
            );
        }

        return redirect()->route('hudsyn.settings.index')
                       ->with('success', 'Facebook authenticated successfully.');
    }

    /**
     * Initiate Instagram OAuth flow
     */
    public function instagramAuth()
    {
        // Instagram uses Facebook's OAuth flow
        return $this->facebookAuth();
    }

    /**
     * Handle Instagram OAuth callback
     */
    public function instagramCallback(Request $request)
    {
        // Instagram uses Facebook's OAuth flow
        return $this->facebookCallback($request);
    }

}
