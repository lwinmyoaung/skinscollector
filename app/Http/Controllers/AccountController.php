<?php

namespace App\Http\Controllers;

use App\Models\AdminMcggProduct;
use App\Models\AdminMlProduct;
use App\Models\AdminPubgProduct;
use App\Models\AdminWwmProduct;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\ProductPrice;
use App\Models\TopUp;
use App\Models\User;
use App\Models\UserMcggProduct;
use App\Models\UserMlProduct;
use App\Models\UserPubgProduct;
use App\Models\UserWwmProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        }

    public function index(Request $request)
    {
        $query = TopUp::latest();

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $topups = $query->get();

        $paymentMethods = PaymentMethod::all();

        return view('admin.orders', compact('topups', 'paymentMethods'));
    }

    public function advertiseIndex()
    {
        $directory = 'ads/slides';

        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $files = collect(\Illuminate\Support\Facades\Storage::disk('public')->files($directory))
            ->filter(function ($file) use ($allowedExtensions) {
                return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedExtensions, true);
            })
            ->map(function ($file) {
                return [
                    'name' => basename($file),
                    'url' => asset('storage/'.$file),
                    'size' => \Illuminate\Support\Facades\Storage::disk('public')->size($file),
                    'modified' => \Illuminate\Support\Facades\Storage::disk('public')->lastModified($file),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        $entryDirectory = 'ads/entry';

        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($entryDirectory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($entryDirectory);
        }

        $entryFiles = collect(\Illuminate\Support\Facades\Storage::disk('public')->files($entryDirectory))
            ->filter(function ($file) use ($allowedExtensions) {
                return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedExtensions, true);
            })
            ->map(function ($file) {
                return [
                    'name' => basename($file),
                    'url' => asset('storage/'.$file),
                    'size' => \Illuminate\Support\Facades\Storage::disk('public')->size($file),
                    'modified' => \Illuminate\Support\Facades\Storage::disk('public')->lastModified($file),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        $entryAd = $entryFiles->first();

        $iconDirectory = 'logo';

        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($iconDirectory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($iconDirectory);
        }

        $iconFiles = collect(\Illuminate\Support\Facades\Storage::disk('public')->files($iconDirectory))
            ->filter(function ($file) use ($allowedExtensions) {
                return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedExtensions, true);
            })
            ->map(function ($file) {
                return [
                    'name' => basename($file),
                    'url' => asset('storage/'.$file),
                    'size' => \Illuminate\Support\Facades\Storage::disk('public')->size($file),
                    'modified' => \Illuminate\Support\Facades\Storage::disk('public')->lastModified($file),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        $appIcon = $iconFiles->first();

        return view('admin.dashboard', [
            'slides' => $files,
            'entryAd' => $entryAd,
            'appIcon' => $appIcon,
        ]);
    }

    public function advertiseStore(Request $request)
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        foreach ($validated['images'] as $image) {
            $image->store('ads/slides', 'public');
        }

        return redirect()->route('admin.ads')->with('success', 'Slides uploaded successfully.');
    }

    public function advertiseDestroy(string $filename)
    {
        if ($filename !== basename($filename)) {
            abort(400);
        }

        $path = 'ads/slides/'.$filename;

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }

        return redirect()->route('admin.ads')->with('success', 'Slide deleted successfully.');
    }

    public function entryAdStore(Request $request)
    {
        $validated = $request->validate([
            'entry_image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $directory = 'ads/entry';

        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
        }

        $existingFiles = \Illuminate\Support\Facades\Storage::disk('public')->files($directory);
        foreach ($existingFiles as $file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
        }

        $validated['entry_image']->store($directory, 'public');

        return redirect()->route('admin.ads')->with('success', 'Entry ad image updated successfully.');
    }

    public function entryAdDestroy()
    {
        $directory = 'ads/entry';

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            $files = \Illuminate\Support\Facades\Storage::disk('public')->files($directory);
            foreach ($files as $file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
            }
        }

        return redirect()->route('admin.ads')->with('success', 'Entry ad image removed successfully.');
    }

    public function appIconStore(Request $request)
    {
        $validated = $request->validate([
            'app_icon' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $directory = 'logo';

        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
        }

        $existingFiles = \Illuminate\Support\Facades\Storage::disk('public')->files($directory);
        foreach ($existingFiles as $file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
        }

        $validated['app_icon']->store($directory, 'public');

        return redirect()->route('admin.dashboard')->with('success', 'App icon updated successfully.');
    }

    public function appIconDestroy()
    {
        $directory = 'logo';

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            $files = \Illuminate\Support\Facades\Storage::disk('public')->files($directory);
            foreach ($files as $file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'App icon removed successfully.');
    }

    public function cookieAndApiIndex()
    {
        $defaultSoMiniappCookie = (string) config('services.so_miniapp.cookie', '');
        $defaultSoMiniappBaseUri = (string) config('services.so_miniapp.base_uri', 'https://so.miniapp.zone');

        $soMiniappCookie = $this->getEncryptedSetting('settings.so_miniapp.cookie_enc', $defaultSoMiniappCookie);
        $soMiniappBaseUri = $this->getEncryptedSetting('settings.so_miniapp.base_uri_enc', $defaultSoMiniappBaseUri);

        $verify = Cache::get('settings.so_miniapp.verify');
        if ($verify === null) {
            $verify = (bool) config('services.so_miniapp.verify', true);
        }

        $timeoutValue = Cache::get('settings.so_miniapp.timeout');
        $timeout = is_numeric($timeoutValue) ? (int) $timeoutValue : (int) config('services.so_miniapp.timeout', 15);

        $defaultMlbbApiBaseUrl = (string) env('MLBB_API_URL', '');
        $defaultPubgApiProductsUrl = $defaultMlbbApiBaseUrl !== '' ? rtrim($defaultMlbbApiBaseUrl, '/').'/products' : '';

        $mlbbApiBaseUrl = $this->getEncryptedSetting('settings.api.mlbb_base_url_enc', '');
        $pubgApiProductsUrlInput = $this->getEncryptedSetting('settings.api.pubg_products_url_enc', '');
        $pubgApiProductsUrlResolved = '';
        if ($pubgApiProductsUrlInput !== '') {
            if (str_starts_with($pubgApiProductsUrlInput, 'http://') || str_starts_with($pubgApiProductsUrlInput, 'https://')) {
                $pubgApiProductsUrlResolved = $pubgApiProductsUrlInput;
            } elseif ($mlbbApiBaseUrl !== '') {
                $pubgApiProductsUrlResolved = $this->resolveUrl($mlbbApiBaseUrl, $pubgApiProductsUrlInput);
            }
        }

        $mcggApiProductsUrlInput = $this->getEncryptedSetting('settings.api.mcgg_products_url_enc', '');
        $mcggApiProductsUrlResolved = '';
        if ($mcggApiProductsUrlInput !== '') {
            if (str_starts_with($mcggApiProductsUrlInput, 'http://') || str_starts_with($mcggApiProductsUrlInput, 'https://')) {
                $mcggApiProductsUrlResolved = $mcggApiProductsUrlInput;
            } elseif ($mlbbApiBaseUrl !== '') {
                $mcggApiProductsUrlResolved = $this->resolveUrl($mlbbApiBaseUrl, $mcggApiProductsUrlInput);
            }
        }

        $wwmApiProductsUrlInput = $this->getEncryptedSetting('settings.api.wwm_products_url_enc', '');
        $wwmApiProductsUrlResolved = '';
        if ($wwmApiProductsUrlInput !== '') {
            if (str_starts_with($wwmApiProductsUrlInput, 'http://') || str_starts_with($wwmApiProductsUrlInput, 'https://')) {
                $wwmApiProductsUrlResolved = $wwmApiProductsUrlInput;
            } elseif ($mlbbApiBaseUrl !== '') {
                $wwmApiProductsUrlResolved = $this->resolveUrl($mlbbApiBaseUrl, $wwmApiProductsUrlInput);
            }
        }

        $buildMlbbUrls = function (string $baseUrl): array {
            $baseUrl = rtrim($baseUrl, '/');
            if (trim($baseUrl) === '') {
                return [
                    'myanmar' => '',
                    'malaysia' => '',
                    'philippines' => '',
                    'singapore' => '',
                    'indonesia' => '',
                    'russia' => '',
                ];
            }

            return [
                'myanmar' => $baseUrl.'/mlproductsmm',
                'malaysia' => $baseUrl.'/mlproductsmy',
                'philippines' => $baseUrl.'/mlproductsph',
                'singapore' => $baseUrl.'/mlproductssg',
                'indonesia' => $baseUrl.'/mlproductsind',
                'russia' => $baseUrl.'/mlproductsru',
            ];
        };

        $mlbbApiDefaultUrls = $buildMlbbUrls($defaultMlbbApiBaseUrl);
        $mlbbApiUrlInputs = [];
        $mlbbApiUrlsResolved = [];
        foreach (array_keys($mlbbApiDefaultUrls) as $region) {
            $input = $this->getEncryptedSetting('settings.api.mlbb.url.'.$region.'_enc', '');
            $mlbbApiUrlInputs[$region] = $input;
            if ($input !== '') {
                if (str_starts_with($input, 'http://') || str_starts_with($input, 'https://')) {
                    $mlbbApiUrlsResolved[$region] = $input;
                } elseif ($mlbbApiBaseUrl !== '') {
                    $mlbbApiUrlsResolved[$region] = $this->resolveUrl($mlbbApiBaseUrl, $input);
                } else {
                    $mlbbApiUrlsResolved[$region] = '';
                }
            } else {
                $mlbbApiUrlsResolved[$region] = '';
            }
        }

        $apiBaseUrl = $mlbbApiBaseUrl !== '' ? rtrim($mlbbApiBaseUrl, '/') : '';
        $apiEndpointList = [
            [
                'name' => 'GET /products (PUBG product list)',
                'url' => $pubgApiProductsUrlResolved,
            ],
            [
                'name' => 'GET /mcgg/products (MCGG product list)',
                'url' => $mcggApiProductsUrlResolved,
            ],
            [
                'name' => 'GET /wwm/products (WWM product list)',
                'url' => $wwmApiProductsUrlResolved,
            ],
            [
                'name' => 'GET /mlproducts',
                'url' => $apiBaseUrl !== '' ? $apiBaseUrl.'/mlproducts' : '',
            ],
            [
                'name' => 'GET /mlbb_regions',
                'url' => $apiBaseUrl !== '' ? $apiBaseUrl.'/mlbb_regions' : '',
            ],
        ];

        $cacheStatus = [
            'mlbb' => Cache::has('mlbb.products'),
            'mlbb_message' => Cache::get('mlbb.status_message', 'Not Checked'),
            'pubg' => Cache::has('pubg.products'),
            'pubg_message' => Cache::get('pubg.status_message', 'Not Checked'),
            'mcgg' => Cache::has('mcgg.products'),
            'mcgg_message' => Cache::get('mcgg.status_message', 'Not Checked'),
            'wwm' => Cache::has('wwm.products'),
            'wwm_message' => Cache::get('wwm.status_message', 'Not Checked'),
        ];

        return view('admin.cookieandapi', [
            'cacheStatus' => $cacheStatus,
            'soMiniappCookie' => $soMiniappCookie,
            'soMiniappBaseUri' => $soMiniappBaseUri,
            'soMiniappVerify' => (bool) $verify,
            'soMiniappTimeout' => (int) $timeout,
            'mlbbApiBaseUrl' => $mlbbApiBaseUrl,
            'pubgApiProductsUrlInput' => $pubgApiProductsUrlInput,
            'pubgApiProductsUrlResolved' => $pubgApiProductsUrlResolved,
            'mcggApiProductsUrlInput' => $mcggApiProductsUrlInput,
            'mcggApiProductsUrlResolved' => $mcggApiProductsUrlResolved,
            'wwmApiProductsUrlInput' => $wwmApiProductsUrlInput,
            'wwmApiProductsUrlResolved' => $wwmApiProductsUrlResolved,
            'defaultSoMiniappCookie' => $defaultSoMiniappCookie,
            'defaultSoMiniappBaseUri' => $defaultSoMiniappBaseUri,
            'defaultMlbbApiBaseUrl' => $defaultMlbbApiBaseUrl,
            'defaultPubgApiProductsUrl' => $defaultPubgApiProductsUrl,
            'mlbbApiDefaultUrls' => $mlbbApiDefaultUrls,
            'mlbbApiUrlInputs' => $mlbbApiUrlInputs,
            'mlbbApiUrlsResolved' => $mlbbApiUrlsResolved,
            'apiEndpointList' => $apiEndpointList,
        ]);
    }

    public function cookieAndApiUpdate(Request $request)
    {
        $validated = $request->validate([
            'so_miniapp_base_uri' => ['nullable', 'string', 'max:255'],
            'so_miniapp_cookie' => ['nullable', 'string', 'max:20000'],
            'so_miniapp_verify' => ['nullable'],
            'so_miniapp_timeout' => ['nullable', 'integer', 'min:1', 'max:60'],
            'mlbb_api_base_url' => ['nullable', 'string', 'max:255'],
            'mlbb_api_urls' => ['nullable', 'array'],
            'mlbb_api_urls.*' => ['nullable', 'string', 'max:255'],
            'pubg_api_products_url' => ['nullable', 'string', 'max:255'],
            'mcgg_api_products_url' => ['nullable', 'string', 'max:255'],
            'wwm_api_products_url' => ['nullable', 'string', 'max:255'],
        ]);

        $toEndpointPath = function (string $value): string {
            $v = trim($value);
            if ($v === '') {
                return '';
            }

            if (str_starts_with($v, 'http://') || str_starts_with($v, 'https://')) {
                $parts = parse_url($v);
                if (! is_array($parts)) {
                    return '';
                }
                $path = (string) ($parts['path'] ?? '');
                $query = (string) ($parts['query'] ?? '');
                $out = ltrim($path, '/');
                if ($out === '') {
                    return '';
                }
                if ($query !== '') {
                    $out .= '?'.$query;
                }

                return $out;
            }

            return ltrim($v, '/');
        };

        $baseUri = trim((string) ($validated['so_miniapp_base_uri'] ?? ''));
        if ($baseUri === '') {
            Cache::forget('settings.so_miniapp.base_uri_enc');
        } else {
            $this->putEncryptedSetting('settings.so_miniapp.base_uri_enc', $baseUri);
        }

        $cookie = trim((string) ($validated['so_miniapp_cookie'] ?? ''));
        if ($cookie === '') {
            Cache::forget('settings.so_miniapp.cookie_enc');
        } else {
            $this->putEncryptedSetting('settings.so_miniapp.cookie_enc', $cookie);
        }

        Cache::forever('settings.so_miniapp.verify', $request->boolean('so_miniapp_verify'));

        if (isset($validated['so_miniapp_timeout']) && $validated['so_miniapp_timeout'] !== null && $validated['so_miniapp_timeout'] !== '') {
            Cache::forever('settings.so_miniapp.timeout', (int) $validated['so_miniapp_timeout']);
        } else {
            Cache::forget('settings.so_miniapp.timeout');
        }

        $mlbbBaseUrl = trim((string) ($validated['mlbb_api_base_url'] ?? ''));
        if ($mlbbBaseUrl === '') {
            Cache::forget('settings.api.mlbb_base_url_enc');
        } else {
            $this->putEncryptedSetting('settings.api.mlbb_base_url_enc', $mlbbBaseUrl);
        }

        $allowedRegions = ['myanmar', 'malaysia', 'philippines', 'singapore', 'indonesia', 'russia'];
        $mlbbUrls = $validated['mlbb_api_urls'] ?? [];
        if (! is_array($mlbbUrls)) {
            $mlbbUrls = [];
        }
        foreach ($allowedRegions as $region) {
            $value = $toEndpointPath((string) ($mlbbUrls[$region] ?? ''));
            $key = 'settings.api.mlbb.url.'.$region.'_enc';
            if ($value === '') {
                Cache::forget($key);
            } else {
                $this->putEncryptedSetting($key, $value);
            }
        }

        $pubgUrl = $toEndpointPath((string) ($validated['pubg_api_products_url'] ?? ''));
        if ($pubgUrl === '') {
            Cache::forget('settings.api.pubg_products_url_enc');
        } else {
            $this->putEncryptedSetting('settings.api.pubg_products_url_enc', $pubgUrl);
        }

        $mcggUrl = $toEndpointPath((string) ($validated['mcgg_api_products_url'] ?? ''));
        if ($mcggUrl === '') {
            Cache::forget('settings.api.mcgg_products_url_enc');
        } else {
            $this->putEncryptedSetting('settings.api.mcgg_products_url_enc', $mcggUrl);
        }

        $wwmUrl = $toEndpointPath((string) ($validated['wwm_api_products_url'] ?? ''));
        if ($wwmUrl === '') {
            Cache::forget('settings.api.wwm_products_url_enc');
        } else {
            $this->putEncryptedSetting('settings.api.wwm_products_url_enc', $wwmUrl);
        }

        // Auto-fetch data to verify settings and populate cache
        $messages = ['Settings saved.'];

        try {
            // MLBB
            $res = $this->performMlbbFetch('all');
            $messages[] = 'MLBB: ' . ($res['success'] ? 'Active' : 'Inactive (' . $res['message'] . ')');

            // PUBG
            $res = $this->performPubgFetch();
            $messages[] = 'PUBG: ' . ($res['success'] ? 'Active' : 'Inactive (' . $res['message'] . ')');

            // MCGG
            $res = $this->performMcggFetch();
            $messages[] = 'MCGG: ' . ($res['success'] ? 'Active' : 'Inactive (' . $res['message'] . ')');

            // WWM
            $res = $this->performWwmFetch();
            $messages[] = 'WWM: ' . ($res['success'] ? 'Active' : 'Inactive (' . $res['message'] . ')');
        } catch (\Exception $e) {
            $messages[] = 'Fetch Error: ' . $e->getMessage();
        }

        return back()->with('success', implode(' | ', $messages));
    }

    private function getEncryptedSetting(string $key, string $default = ''): string
    {
        $raw = Cache::get($key);
        if (! is_string($raw) || $raw === '') {
            return $default;
        }

        try {
            return Crypt::decryptString($raw);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    private function putEncryptedSetting(string $key, string $value): void
    {
        Cache::forever($key, Crypt::encryptString($value));
    }

    private function resolveUrl(string $baseUrl, string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $baseUrl = rtrim(trim($baseUrl), '/');

        return $baseUrl.'/'.ltrim($value, '/');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show() {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function showusers(Request $request)
    {
        $query = User::query();

        // Search by email or ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        // Get paginated users
        $users = $query->orderBy('id', 'desc')->paginate(15);

        // Calculate Stats
        $totalUsers = User::count();

        return view('admin.users', compact('users', 'totalUsers'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting self
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully');
    }


    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user = User::findOrFail($id);

        // Prevent changing own role
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    public function sendUserMessage(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = User::findOrFail($id);

        Notification::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'message' => 'From Admin: '.$data['message'],
            'type' => 'info',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Message sent to user inbox.');
    }

    /**
     * Approve a top-up request.
     * This method handles the approval process using a database transaction to ensure data integrity.
     * It updates the top-up status, increments the user's balance, and sends a notification.
     */
    public function approve($id)
    {
        // Start a database transaction to ensure all operations succeed or fail together
        DB::beginTransaction();

        try {
            // Find the top-up record and lock it for update to prevent race conditions
            $topup = TopUp::lockForUpdate()->findOrFail($id);

            // Check if the top-up has already been processed to avoid double approval
            if ($topup->status !== 'pending') {
                return back()->with('error', 'This top up is already processed.');
            }

            // Find the user associated with the top-up
            $user = User::findOrFail($topup->user_id);

            // Add the top-up amount to the user's balance
            $user->balance += $topup->amount;
            $user->save();

            // Update the top-up status to 'approved'
            $topup->status = 'approved';
            $topup->save();

            // Create a notification for the user
            $identity = $user->phone ?? $user->email;
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Topup Approved',
                'message' => "Hello {$identity}, your top up of ".number_format($topup->amount).' MMK has been approved. Your current balance is '.number_format($user->balance).' MMK.',
                'type' => 'success',
            ]);

            // Commit the transaction if all operations are successful
            DB::commit();

            return back()->with('success', 'Top up approved and balance updated.');

        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();

            return back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Cancel a top-up request.
     * This method cancels a pending top-up and sends a notification to the user.
     */
    public function cancel($id)
    {
        $topup = TopUp::findOrFail($id);

        // Check if the top-up has already been processed to prevent modifying completed transactions
        if ($topup->status !== 'pending') {
            return back()->with('error', 'This top up is already processed.');
        }

        $topup->status = 'cancelled';
        $topup->save();

        // Notify User about the cancellation
        Notification::create([
            'user_id' => $topup->user_id,
            'title' => 'Top Up Cancelled',
            'message' => 'Your top up of '.number_format($topup->amount).' MMK has been cancelled.',
            'type' => 'error',
        ]);

        return back()->with('success', 'Top up has been cancelled.');
    }

    public function deleteTopup($id)
    {
        $topup = TopUp::findOrFail($id);

        // Delete the transaction image from storage
        if ($topup->transaction_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($topup->transaction_image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($topup->transaction_image);
        }

        // Delete the record
        $topup->delete();

        return back()->with('success', 'Top up record and image deleted successfully.');
    }

    public function deleteTopupsByDate(Request $request)
    {
        $request->validate([
            'delete_date' => 'required|date|before_or_equal:today',
        ]);

        $date = $request->delete_date;

        // Find records created before the selected date (using created_at < date + 1 day to include the selected date fully or strictly before)
        // User request: "delete with last date" usually means delete everything up to that date.
        // Let's assume strictly before the start of the next day (so including the selected date).
        // If user selects 2023-01-01, they probably want to delete 2023-01-01 and older.

        $topups = TopUp::whereDate('created_at', '<=', $date)->get();

        $count = 0;
        foreach ($topups as $topup) {
            // Delete image
            if ($topup->transaction_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($topup->transaction_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($topup->transaction_image);
            }

            // Delete record
            $topup->delete();
            $count++;
        }

        return back()->with('success', "$count records older than or equal to $date have been permanently deleted.");
    }

    public function mlbbPrices(Request $request)
    {
        $region = $request->get('region');
        $normalize = function ($r) {
            $x = strtolower((string) $r);
            if (str_contains($x, 'myan') || $x === 'mm' || $x === 'mmk') {
                return 'myanmar';
            }
            if (str_contains($x, 'malay') || $x === 'my' || $x === 'myr') {
                return 'malaysia';
            }
            if (str_contains($x, 'phil') || $x === 'ph' || $x === 'php') {
                return 'philippines';
            }
            if (str_contains($x, 'sing') || $x === 'sg' || $x === 'sgd') {
                return 'singapore';
            }
            if (str_contains($x, 'indo') || $x === 'id' || $x === 'idr') {
                return 'indonesia';
            }
            if (str_contains($x, 'rus') || $x === 'ru' || $x === 'rub') {
                return 'russia';
            }

            return $x;
        };
        $normRegion = $region ? $normalize($region) : null;
        $overrides = collect();
        if (Schema::hasTable('product_prices')) {
            $ovQuery = ProductPrice::query();
            if ($normRegion) {
                $ovQuery->whereRaw('LOWER(region) = ?', [strtolower($normRegion)]);
            }
            $overrides = $ovQuery
                ->get()
                ->keyBy(fn ($pp) => strtolower($pp->product_id).'|'.strtolower($pp->region));
        }

        // 1. Fetch from Database (Admin DB / Cache)
        $query = AdminMlProduct::query();

        // Filter by Region
        if ($normRegion) {
            $query->whereRaw('LOWER(region) = ?', [$normRegion]);
        }

        // Filter for Diamonds/Passes
        $query->where(function ($q) {
            $q->whereRaw('LOWER(category) = ?', ['diamonds'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%diamond%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%pass%']);
        });

        // Get Products
        $displayProducts = $query->get()->map(function ($item) use ($overrides) {
            // Apply overrides if they exist
            $key = strtolower($item->product_id).'|'.strtolower($item->region);
            if (isset($overrides[$key])) {
                $item->price = $overrides[$key]->price;
            }

            return (object) [
                'product_id' => $item->product_id,
                'name' => $item->name,
                'category' => $item->category,
                'region' => $item->region,
                'diamonds' => (int) $item->diamonds,
                'price' => (int) $item->price,
            ];
        });

        // Sort
        $displayProducts = $displayProducts->sortBy(function ($p) {
            $n = strtolower($p->name ?? '');
            $priority = 3;
            if (str_contains($n, 'weekly pass')) {
                $priority = 0;
            } elseif (str_contains($n, 'twilight pass')) {
                $priority = 1;
            } elseif (str_contains($n, 'frc')) {
                $priority = 2;
            }

            return $priority * 1000000000 + (int) ($p->price ?? 0);
        })->values();

        // If empty and region is selected, try auto-fetch once (Optional, kept for convenience but safe due to try-catch in fetch)
        if ($displayProducts->isEmpty() && $region) {
            // We can redirect to fetch logic or just return empty view.
            // Let's return empty view so user must click "Fetch" explicitly as requested.
        }

        return view('admin.diamondpricemanager', [
            'products' => $displayProducts,
            'region' => $normRegion ?? '',
            'overrides' => $overrides,
            'isMlbb' => true,
        ]);
    }

    public function updateMlbbPrices(Request $request)
    {
        $updates = $request->input('updates', []);
        $count = 0;
        foreach ($updates as $u) {
            $pid = $u['product_id'] ?? null;
            $region = $u['region'] ?? null;
            $price = $u['price'] ?? null;
            if ($pid && $region !== null && $price !== null && is_numeric($price) && (float) $price >= 0) {
                $base = AdminMlProduct::where('product_id', $pid)->where('region', $region)->first();
                $ov = ProductPrice::updateOrCreate(
                    ['product_id' => (string) $pid, 'region' => (string) $region],
                    [
                        'name' => $base->name ?? null,
                        'category' => $base->category ?? 'Diamonds',
                        'diamonds' => $base->diamonds ?? 0,
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );
                UserMlProduct::updateOrCreate(
                    ['product_id' => (string) $pid, 'region' => (string) $region],
                    [
                        'name' => $ov->name ?? ($base->name ?? null),
                        'category' => $ov->category ?? ($base->category ?? 'Diamonds'),
                        'diamonds' => (int) ($ov->diamonds ?? ($base->diamonds ?? 0)),
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );
                $count++;
            }
        }

        foreach (['myanmar', 'malaysia', 'philippines', 'singapore', 'indonesia', 'russia'] as $r) {
            Cache::forget('mlbb.products.'.$r);
        }

        return back()->with('success', $count.' prices updated.');
    }

    public function seedMlbbPrices(Request $request)
    {
        $region = $request->input('region');
        if (! Schema::hasTable('product_prices')) {
            return back()->with('error', 'Price table not found. Please run migrations.');
        }
        $norm = function ($r) {
            $x = strtolower((string) $r);
            if (str_contains($x, 'myan') || $x === 'mm' || $x === 'mmk') {
                return 'myanmar';
            }
            if (str_contains($x, 'malay') || $x === 'my' || $x === 'myr') {
                return 'malaysia';
            }
            if (str_contains($x, 'phil') || $x === 'ph' || $x === 'php') {
                return 'philippines';
            }
            if (str_contains($x, 'sing') || $x === 'sg' || $x === 'sgd') {
                return 'singapore';
            }
            if (str_contains($x, 'indo') || $x === 'id' || $x === 'idr') {
                return 'indonesia';
            }
            if (str_contains($x, 'rus') || $x === 'ru' || $x === 'rub') {
                return 'russia';
            }

            return $x;
        };
        $target = $norm($region);
        $query = AdminMlProduct::query()->where('category', 'Diamonds');
        if (! empty($target)) {
            $query->whereRaw('LOWER(region) = ?', [$target]);
        }
        $items = $query->get();
        $count = 0;
        foreach ($items as $it) {
            ProductPrice::updateOrCreate(
                ['product_id' => (string) $it->product_id, 'region' => (string) $norm($it->region)],
                [
                    'name' => $it->name,
                    'category' => $it->category,
                    'diamonds' => (int) $it->diamonds,
                    'price' => (int) $it->price,
                    'status' => 1,
                ]
            );
            $count++;
        }

        return back()->with('success', $count.' items seeded.');
    }

    public function fetchMlbbFromApi(Request $request)
    {
        $region = $request->input('region', 'myanmar');
        $result = $this->performMlbbFetch($region);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        $redirectRegion = $result['redirect_region'] ?? '';
        
        return redirect()->route('admin.mlbb.prices', ['region' => $redirectRegion])->with('success', $result['message']);
    }

    private function performMlbbFetch(string $requestedRegion = 'myanmar'): array
    {
        $baseUrl = $this->getEncryptedSetting('settings.api.mlbb_base_url_enc', '');
        if (trim($baseUrl) === '') {
            $msg = 'Please set MLBB API Base URL in Cookie/API Manager.';
            Cache::forever('mlbb.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }
        $baseUrl = rtrim($baseUrl, '/');
        $allowedRegions = ['myanmar', 'malaysia', 'philippines', 'singapore', 'indonesia', 'russia'];
        $api = [];
        $missing = [];
        foreach ($allowedRegions as $region) {
            $input = $this->getEncryptedSetting('settings.api.mlbb.url.'.$region.'_enc', '');
            if (trim($input) === '') {
                $missing[] = $region;

                continue;
            }
            $api[$region] = $this->resolveUrl($baseUrl, $input);
        }

        $requested = strtolower(trim($requestedRegion));
        if ($requested === '' || $requested === 'all') {
            if (! empty($missing)) {
                return ['success' => false, 'message' => 'Missing MLBB API URL for: '.implode(', ', $missing).'. Set them in Cookie/API Manager.'];
            }
            $regionsToFetch = array_keys($api);
        } elseif (isset($api[$requested])) {
            $regionsToFetch = [$requested];
        } else {
            if (in_array($requested, $allowedRegions, true)) {
                return ['success' => false, 'message' => 'Missing MLBB API URL for: '.$requested.'. Set it in Cookie/API Manager.'];
            }

            return ['success' => false, 'message' => 'Invalid region selected'];
        }

        $normalizeKeys = function (array $item): array {
            $out = [];
            foreach ($item as $k => $v) {
                if (is_string($k)) {
                    $out[strtolower($k)] = $v;
                }
            }

            return $out;
        };
        $extractProductId = function ($item, $fallbackKey = null) use ($normalizeKeys) {
            if (! is_array($item)) {
                return null;
            }
            $x = $normalizeKeys($item);
            foreach (['product_id', 'productid', 'id', 'sku', 'code'] as $k) {
                if (isset($x[$k]) && $x[$k] !== null && $x[$k] !== '') {
                    return (string) $x[$k];
                }
            }
            foreach (['product', 'package', 'item'] as $container) {
                if (isset($x[$container]) && is_array($x[$container])) {
                    $inner = $normalizeKeys($x[$container]);
                    foreach (['product_id', 'productid', 'id', 'sku', 'code'] as $k) {
                        if (isset($inner[$k]) && $inner[$k] !== null && $inner[$k] !== '') {
                            return (string) $inner[$k];
                        }
                    }
                }
            }
            if ($fallbackKey !== null && $fallbackKey !== '') {
                return (string) $fallbackKey;
            }

            return null;
        };
        $extractPrice = function (array $item, string $region): int {
            $x = [];
            foreach ($item as $k => $v) {
                if (is_string($k)) {
                    $x[strtolower($k)] = $v;
                }
            }
            $candidatesByRegion = [
                'myanmar' => ['price_mmk', 'mmk', 'mmk_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
                'malaysia' => ['price_myr', 'myr', 'myr_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
                'philippines' => ['price_php', 'php', 'php_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
                'singapore' => ['price_sgd', 'sgd', 'sgd_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
                'indonesia' => ['price_idr', 'idr', 'idr_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
                'russia' => ['price_rub', 'rub', 'rub_price', 'local_price', 'needed', 'need', 'price', 'amount', 'value', 'cost', 'price_value', 'price_int', 'sale_price', 'current_price'],
            ];
            $keys = $candidatesByRegion[$region] ?? ['price', 'amount'];
            if (isset($x['prices']) && is_array($x['prices'])) {
                $map = [];
                foreach ($x['prices'] as $k => $v) {
                    if (is_string($k)) {
                        $map[strtolower($k)] = $v;
                    }
                }
                $regionKeyMap = [
                    'myanmar' => ['mmk'],
                    'malaysia' => ['myr'],
                    'philippines' => ['php'],
                    'singapore' => ['sgd'],
                    'indonesia' => ['idr'],
                    'russia' => ['rub'],
                ];
                foreach ($regionKeyMap[$region] ?? [] as $rk) {
                    if (isset($map[$rk])) {
                        return (int) preg_replace('/[^\d]/', '', (string) $map[$rk]);
                    }
                }
            }
            foreach ($keys as $k) {
                $lk = strtolower($k);
                if (isset($x[$lk])) {
                    return (int) preg_replace('/[^\d]/', '', (string) $x[$lk]);
                }
            }

            return 0;
        };
        $extractDiamonds = function (array $item): int {
            $x = [];
            foreach ($item as $k => $v) {
                if (is_string($k)) {
                    $x[strtolower($k)] = $v;
                }
            }
            if (isset($x['diamonds']) && is_numeric($x['diamonds'])) {
                return (int) $x['diamonds'];
            }
            if (isset($x['diamond']) && is_numeric($x['diamond'])) {
                return (int) $x['diamond'];
            }
            $name = $x['name'] ?? $x['product_name'] ?? $x['title'] ?? null;
            if ($name !== null) {
                if (preg_match('/(\d+)\s*💎/u', (string) $name, $m)) {
                    return (int) $m[1];
                }
                if (preg_match('/(\d+)\s*(diamond|diamonds)/i', (string) $name, $m)) {
                    return (int) $m[1];
                }
            }

            return 0;
        };
        $totalSaved = 0;
        $savedByRegion = [];
        $errors = [];
        $statsByRegion = [];

        foreach ($regionsToFetch as $region) {
            $url = $api[$region];
            $data = null;

            try {
                $resp = Http::timeout(12)->get($url);
                if (! $resp->successful()) {
                    $errors[] = $region.': '.$resp->status();

                    continue;
                }
                $data = $resp->json();
            } catch (\Exception $e) {
                $errors[] = $region.': '.$e->getMessage();

                continue;
            }

            if (is_array($data) && isset($data['data']) && is_array($data['data'])) {
                $data = $data['data'];
            } elseif (is_array($data) && isset($data['items']) && is_array($data['items'])) {
                $data = $data['items'];
            }

            if (is_array($data) && ! empty($data)) {
                $keys = array_keys($data);
                $isList = $keys === range(0, count($keys) - 1);
                if ($isList) {
                    $data = ['Diamonds' => $data];
                }
            } else {
                $data = ['Diamonds' => []];
            }

            $saved = 0;
            $scanned = 0;
            $noId = 0;
            foreach ($data as $category => $items) {
                if (! is_array($items)) {
                    continue;
                }
                $itemKeys = array_keys($items);
                $itemsIsList = $itemKeys === range(0, count($itemKeys) - 1);

                foreach ($items as $itemKey => $item) {
                    $scanned++;
                    $pid = $extractProductId($item, $itemsIsList ? null : $itemKey);
                    $itemLower = is_array($item) ? $normalizeKeys($item) : [];
                    $name = $itemLower['name'] ?? $itemLower['product_name'] ?? $itemLower['title'] ?? null;
                    $price = $extractPrice($item, $region);
                    $diamonds = $extractDiamonds($item);
                    if (! $pid) {
                        $noId++;

                        continue;
                    }

                    $saveCategory = 'Diamonds';
                    $ml = AdminMlProduct::updateOrCreate(
                        ['product_id' => (string) $pid, 'region' => $region],
                        [
                            'name' => $name,
                            'category' => $saveCategory,
                            'diamonds' => (int) $diamonds,
                            'price' => (int) $price,
                            'status' => 1,
                        ]
                    );
                    $saved++;
                }
            }

            $savedByRegion[$region] = $saved;
            $statsByRegion[$region] = ['scanned' => $scanned, 'no_id' => $noId, 'url' => $url];
            $totalSaved += $saved;
        }

        if ($totalSaved <= 0) {
            $msg = 'No items saved.';
            foreach ($statsByRegion as $r => $st) {
                $msg .= ' '.$r.' scanned='.$st['scanned'].' no_id='.$st['no_id'];
            }
            if (! empty($errors)) {
                $msg .= ' Errors: '.implode(', ', $errors);
            }

            Cache::forever('mlbb.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }

        $redirectRegion = count($regionsToFetch) === 1 ? $regionsToFetch[0] : '';
        $success = 'Fetched: ';
        foreach ($savedByRegion as $r => $c) {
            $success .= $r.'='.$c.' ';
        }
        if (! empty($errors)) {
            $success .= '| Errors: '.implode(', ', $errors);
        }

        // Clear cache
        Cache::forget('mlbb.products');
        Cache::forever('mlbb.status_message', 'Active');

        return [
            'success' => true,
            'message' => trim($success),
            'redirect_region' => $redirectRegion,
        ];
    }

    public function bulkUpdateMlbbPrices(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric',
        ]);

        $percentage = (float) $request->input('percentage');
        $region = $request->input('region');
        $normalize = function ($r) {
            $x = strtolower((string) $r);
            if (str_contains($x, 'myan') || $x === 'mm' || $x === 'mmk') {
                return 'myanmar';
            }
            if (str_contains($x, 'malay') || $x === 'my' || $x === 'myr') {
                return 'malaysia';
            }
            if (str_contains($x, 'phil') || $x === 'ph' || $x === 'php') {
                return 'philippines';
            }
            if (str_contains($x, 'sing') || $x === 'sg' || $x === 'sgd') {
                return 'singapore';
            }
            if (str_contains($x, 'indo') || $x === 'id' || $x === 'idr') {
                return 'indonesia';
            }
            if (str_contains($x, 'rus') || $x === 'ru' || $x === 'rub') {
                return 'russia';
            }

            return $x;
        };
        $target = $region && $region !== 'all' ? $normalize($region) : null;

        $query = AdminMlProduct::query();
        if ($target) {
            $query->whereRaw('LOWER(region) = ?', [$target]);
        }
        $query->where(function ($q) {
            $q->whereRaw('LOWER(category) = ?', ['diamonds'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%diamond%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%pass%']);
        });

        $items = $query->get();
        $count = 0;

        foreach ($items as $base) {
            $newPrice = $base->price * (1 + ($percentage / 100));
            $newPrice = (int) round($newPrice);

            $ov = ProductPrice::updateOrCreate(
                ['product_id' => (string) $base->product_id, 'region' => (string) $base->region],
                [
                    'name' => $base->name,
                    'category' => $base->category,
                    'diamonds' => $base->diamonds,
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );

            UserMlProduct::updateOrCreate(
                ['product_id' => (string) $base->product_id, 'region' => (string) $base->region],
                [
                    'name' => $ov->name,
                    'category' => $ov->category,
                    'diamonds' => (int) $ov->diamonds,
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );
            $count++;
        }

        return back()->with('success', "Updated $count items by $percentage%.");
    }

    public function pubgPrices(Request $request)
    {
        $overrides = collect();
        if (Schema::hasTable('product_prices')) {
            $ovQuery = ProductPrice::query();
            $overrides = $ovQuery->get()->keyBy(function ($pp) {
                return strtolower($pp->product_id).'|'.strtolower($pp->region);
            });
        }

        $query = AdminPubgProduct::query();
        $displayProducts = $query->get()->map(function ($item) use ($overrides) {
            $key = 'pubg_'.strtolower($item->product_id).'|'.strtolower($item->region);

            $price = $item->price;
            if (isset($overrides[$key])) {
                $price = $overrides[$key]->price;
            }

            return (object) [
                'product_id' => $item->product_id,
                'name' => $item->name,
                'category' => $item->category,
                'region' => $item->region,
                'uc' => (int) $item->uc,
                'price' => (int) $price,
            ];
        });

        $displayProducts = $displayProducts->sortBy('price')->values();

        return view('admin.diamondpricemanager', [
            'products' => $displayProducts,
            'region' => 'global',
            'overrides' => $overrides,
            'isPubg' => true,
        ]);
    }

    public function fetchPubgFromApi(Request $request)
    {
        $result = $this->performPubgFetch();

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    private function performPubgFetch(): array
    {
        $baseUrl = $this->getEncryptedSetting('settings.api.mlbb_base_url_enc', '');
        if (trim($baseUrl) === '') {
            $msg = 'Please set MLBB API Base URL in Cookie/API Manager.';
            Cache::forever('pubg.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }
        $url = $this->getEncryptedSetting('settings.api.pubg_products_url_enc', '');
        if (trim($url) === '') {
            $msg = 'Please set PUBG Products API URL in Cookie/API Manager.';
            Cache::forever('pubg.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }
        $url = $this->resolveUrl($baseUrl, $url);
        try {
            $resp = Http::timeout(12)->get($url);
            if (! $resp->successful()) {
                $msg = 'API Error: '.$resp->status();
                Cache::forever('pubg.status_message', $msg);
                return ['success' => false, 'message' => $msg];
            }
            $data = $resp->json();
        } catch (\Exception $e) {
            $msg = 'Fetch failed: '.$e->getMessage();
            Cache::forever('pubg.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }

        $count = 0;
        $seenProductIds = [];
        if (is_array($data)) {
            if (isset($data['data']) && is_array($data['data'])) {
                $data = $data['data'];
            } elseif (isset($data['items']) && is_array($data['items'])) {
                $data = $data['items'];
            }
            $normalizeKeys = function (array $item): array {
                $out = [];
                foreach ($item as $k => $v) {
                    if (is_string($k)) {
                        $out[strtolower($k)] = $v;
                    }
                }

                return $out;
            };

            $keys = array_keys($data);
            $isList = $keys === range(0, count($keys) - 1);
            if ($isList) {
                $data = ['UC' => $data];
            }

            AdminPubgProduct::where('region', 'global')->delete();

            foreach ($data as $categoryName => $items) {
                if (! is_array($items)) {
                    continue;
                }

                $itemKeys = array_keys($items);
                $itemsIsList = $itemKeys === range(0, count($itemKeys) - 1);

                foreach ($items as $itemKey => $item) {
                    $pid = null;
                    $name = null;
                    $price = 0;

                    if (is_array($item)) {
                        $lower = $normalizeKeys($item);
                        $pid = $lower['product_id'] ?? $lower['id'] ?? null;
                        if ($pid === null && ! $itemsIsList) {
                            $pid = $itemKey;
                        }
                        $name = $lower['name'] ?? $lower['title'] ?? null;
                        if (isset($lower['price'])) {
                            $price = (int) preg_replace('/[^\d]/', '', (string) $lower['price']);
                        }
                    }

                    $uc = 0;
                    if (isset($lower) && isset($lower['uc'])) {
                        $uc = $lower['uc'];
                    } elseif ($name && preg_match('/(\d+)\s*UC/i', $name, $m)) {
                        $uc = $m[1];
                    }

                    if ($pid) {
                        AdminPubgProduct::updateOrCreate(
                            ['product_id' => (string) $pid, 'region' => 'global'],
                            [
                                'name' => $name,
                                'uc' => $uc,
                                'price' => (int) $price,
                                'category' => $categoryName,
                                'status' => 1,
                            ]
                        );
                        $seenProductIds[] = (string) $pid;

                        $prefixedId = 'pubg_'.$pid;
                        $ov = ProductPrice::where('product_id', $prefixedId)->where('region', 'global')->first();
                        $effectiveName = $ov->name ?? $name;
                        $effectiveCategory = $ov->category ?? $categoryName;
                        $effectiveUc = (int) ($ov->diamonds ?? $uc);
                        $effectivePrice = (int) ($ov->price ?? $price);

                        UserPubgProduct::updateOrCreate(
                            ['product_id' => (string) $pid, 'region' => 'global'],
                            [
                                'name' => $effectiveName,
                                'category' => $effectiveCategory,
                                'uc' => $effectiveUc,
                                'price' => $effectivePrice,
                                'status' => 1,
                            ]
                        );
                        $count++;
                    }
                }
            }
        }

        if ($seenProductIds !== []) {
            UserPubgProduct::where('region', 'global')
                ->whereNotIn('product_id', array_unique($seenProductIds))
                ->delete();
        }

        Cache::forget('pubg.products');
        Cache::forever('pubg.status_message', 'Active');

        return ['success' => true, 'message' => 'Fetched '.$count.' PUBG items.'];
    }

    public function updatePubgPrices(Request $request)
    {
        $updates = $request->input('updates', []);
        $count = 0;
        foreach ($updates as $u) {
            $pid = $u['product_id'] ?? null;
            $price = $u['price'] ?? null;

            if ($pid && $price !== null && is_numeric($price) && (float) $price >= 0) {
                $base = AdminPubgProduct::where('product_id', $pid)->first();
                $region = $base->region ?? 'global';

                $prefixedId = 'pubg_'.$pid;

                $ov = ProductPrice::updateOrCreate(
                    ['product_id' => $prefixedId, 'region' => $region],
                    [
                        'name' => $base->name ?? null,
                        'category' => $base->category ?? 'UC',
                        'diamonds' => (int) ($base->uc ?? 0),
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );

                UserPubgProduct::updateOrCreate(
                    ['product_id' => (string) $pid, 'region' => $region],
                    [
                        'name' => $ov->name ?? ($base->name ?? null),
                        'category' => $ov->category ?? ($base->category ?? 'UC'),
                        'uc' => (int) ($ov->diamonds ?? ($base->uc ?? 0)),
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );
                $count++;
            }
        }

        Cache::forget('pubg.products');

        return back()->with('success', $count.' PUBG prices updated.');
    }

    public function bulkUpdatePubgPrices(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric',
        ]);

        $percentage = (float) $request->input('percentage');
        $items = AdminPubgProduct::all();
        $count = 0;

        foreach ($items as $base) {
            $newPrice = $base->price * (1 + ($percentage / 100));
            $newPrice = (int) round($newPrice);

            $region = $base->region ?? 'global';
            $prefixedId = 'pubg_'.$base->product_id;

            $ov = ProductPrice::updateOrCreate(
                ['product_id' => $prefixedId, 'region' => $region],
                [
                    'name' => $base->name,
                    'category' => $base->category ?? 'UC',
                    'diamonds' => (int) ($base->uc ?? 0),
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );

            UserPubgProduct::updateOrCreate(
                ['product_id' => (string) $base->product_id, 'region' => $region],
                [
                    'name' => $ov->name,
                    'category' => $ov->category,
                    'uc' => (int) ($ov->diamonds ?? ($base->uc ?? 0)),
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );
            $count++;
        }

        Cache::forget('pubg.products');

        return back()->with('success', "Updated $count PUBG items by $percentage%.");
    }

    public function mcggPrices(Request $request)
    {
        $overrides = collect();
        if (Schema::hasTable('product_prices')) {
            $ovQuery = ProductPrice::query();
            $overrides = $ovQuery->get()->keyBy(function ($pp) {
                return strtolower($pp->product_id).'|'.strtolower($pp->region);
            });
        }

        $query = AdminMcggProduct::query();
        $displayProducts = $query->get()->map(function ($item) use ($overrides) {
            $key = 'mcgg_'.strtolower($item->product_id).'|'.strtolower($item->region);

            $price = $item->price;
            if (isset($overrides[$key])) {
                $price = $overrides[$key]->price;
            }

            return (object) [
                'product_id' => $item->product_id,
                'name' => $item->name,
                'category' => $item->category,
                'region' => $item->region,
                'diamonds' => (string) $item->diamonds,
                'price' => (int) $price,
            ];
        });

        $displayProducts = $displayProducts->sortBy('price')->values();

        return view('admin.diamondpricemanager', [
            'products' => $displayProducts,
            'region' => 'global',
            'overrides' => $overrides,
            'isMcgg' => true,
        ]);
    }

    public function fetchMcggFromApi(Request $request)
    {
        $result = $this->performMcggFetch();

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    private function performMcggFetch(): array
    {
        $baseUrl = $this->getEncryptedSetting('settings.api.mlbb_base_url_enc', '');
        if (trim($baseUrl) === '') {
            $msg = 'Please set MLBB API Base URL in Cookie/API Manager.';
            Cache::forever('mcgg.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }
        $url = $this->getEncryptedSetting('settings.api.mcgg_products_url_enc', '');
        if (trim($url) === '') {
            $msg = 'Please set MCGG Products API URL in Cookie/API Manager.';
            Cache::forever('mcgg.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }
        $url = $this->resolveUrl($baseUrl, $url);
        try {
            $resp = Http::timeout(12)->get($url);
            if (! $resp->successful()) {
                $msg = 'API Error: '.$resp->status();
                Cache::forever('mcgg.status_message', $msg);
                return ['success' => false, 'message' => $msg];
            }
            $data = $resp->json();
        } catch (\Exception $e) {
            $msg = 'Fetch failed: '.$e->getMessage();
            Cache::forever('mcgg.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }

        $count = 0;
        if (is_array($data)) {
            if (isset($data['data']) && is_array($data['data'])) {
                $data = $data['data'];
            } elseif (isset($data['items']) && is_array($data['items'])) {
                $data = $data['items'];
            }
            $normalizeKeys = function (array $item): array {
                $out = [];
                foreach ($item as $k => $v) {
                    if (is_string($k)) {
                        $out[strtolower($k)] = $v;
                    }
                }

                return $out;
            };

            $keys = array_keys($data);
            $isList = $keys === range(0, count($keys) - 1);
            if ($isList) {
                $data = ['Items' => $data];
            }

            foreach ($data as $categoryName => $items) {
                if (! is_array($items)) {
                    continue;
                }

                $itemKeys = array_keys($items);
                $itemsIsList = $itemKeys === range(0, count($itemKeys) - 1);

                foreach ($items as $itemKey => $item) {
                    $pid = null;
                    $name = null;
                    $price = 0;
                    $diamonds = 0;

                    if (is_array($item)) {
                        $lower = $normalizeKeys($item);
                        $pid = $lower['product_id'] ?? $lower['id'] ?? null;
                        if ($pid === null && ! $itemsIsList) {
                            $pid = $itemKey;
                        }
                        $name = $lower['name'] ?? $lower['product_name'] ?? $lower['title'] ?? $pid;
                        if (isset($lower['price'])) {
                            $price = (int) preg_replace('/[^\d]/', '', (string) $lower['price']);
                        }
                        // Try to find diamond count
                        if (isset($lower['diamonds'])) {
                            $diamonds = $lower['diamonds'];
                        } elseif (isset($lower['amount'])) {
                            $diamonds = $lower['amount'];
                        } elseif ($name && preg_match('/(\d+)\s*(diamond|diamonds|dm)/i', $name, $m)) {
                            $diamonds = $m[1];
                        }
                    } else {
                        // Simple key-value?
                        if (! $itemsIsList) {
                            $pid = $itemKey;
                            $name = $item;
                        }
                    }

                    if ($pid) {
                        AdminMcggProduct::updateOrCreate(
                            ['product_id' => (string) $pid, 'region' => 'global'],
                            [
                                'name' => $name,
                                'diamonds' => (string) $diamonds,
                                'price' => (int) $price,
                                'category' => $categoryName,
                                'status' => 1,
                            ]
                        );
                        $count++;
                    }
                }
            }
        }

        Cache::forget('mcgg.products');
        Cache::forever('mcgg.status_message', 'Active');

        return ['success' => true, 'message' => 'Fetched '.$count.' MCGG items.'];
    }

    public function updateMcggPrices(Request $request)
    {
        $updates = $request->input('updates', []);
        $count = 0;
        foreach ($updates as $u) {
            $pid = $u['product_id'] ?? null;
            $price = $u['price'] ?? null;

            if ($pid && $price !== null && is_numeric($price) && (float) $price >= 0) {
                $base = AdminMcggProduct::where('product_id', $pid)->first();
                $region = $base->region ?? 'global';

                $prefixedId = 'mcgg_'.$pid;

                $ov = ProductPrice::updateOrCreate(
                    ['product_id' => $prefixedId, 'region' => $region],
                    [
                        'name' => $base->name ?? null,
                        'category' => $base->category ?? 'Diamonds',
                        'diamonds' => (string) ($base->diamonds ?? 0),
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );

                UserMcggProduct::updateOrCreate(
                    ['product_id' => (string) $pid, 'region' => $region],
                    [
                        'name' => $ov->name ?? ($base->name ?? null),
                        'category' => $ov->category ?? ($base->category ?? 'Diamonds'),
                        'diamonds' => (string) ($ov->diamonds ?? ($base->diamonds ?? 0)),
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );
                $count++;
            }
        }

        return back()->with('success', 'Updated '.$count.' prices.');
    }

    public function bulkUpdateMcggPrices(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric',
        ]);

        $percentage = (float) $request->input('percentage');
        $items = AdminMcggProduct::all();
        $count = 0;

        foreach ($items as $base) {
            $newPrice = $base->price * (1 + ($percentage / 100));
            $newPrice = (int) round($newPrice);

            $region = $base->region ?? 'global';
            $prefixedId = 'mcgg_'.$base->product_id;

            $ov = ProductPrice::updateOrCreate(
                ['product_id' => $prefixedId, 'region' => $region],
                [
                    'name' => $base->name,
                    'category' => $base->category ?? 'Diamonds',
                    'diamonds' => (string) ($base->diamonds ?? 0),
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );

            UserMcggProduct::updateOrCreate(
                ['product_id' => (string) $base->product_id, 'region' => $region],
                [
                    'name' => $ov->name,
                    'category' => $ov->category ?? 'Diamonds',
                    'diamonds' => (string) ($ov->diamonds ?? ($base->diamonds ?? 0)),
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );
            $count++;
        }

        return back()->with('success', "Updated $count MCGG items by $percentage%.");
    }

    public function wwmPrices(Request $request)
    {
        $overrides = collect();
        if (Schema::hasTable('product_prices')) {
            $ovQuery = ProductPrice::query();
            $overrides = $ovQuery->get()->keyBy(function ($pp) {
                return strtolower($pp->product_id).'|'.strtolower($pp->region);
            });
        }

        $query = AdminWwmProduct::query();
        $displayProducts = $query->get()->map(function ($item) use ($overrides) {
            $key = 'wwm_'.strtolower($item->product_id).'|'.strtolower($item->region);

            $price = $item->price;
            if (isset($overrides[$key])) {
                $price = $overrides[$key]->price;
            }

            return (object) [
                'product_id' => $item->product_id,
                'name' => $item->name,
                'category' => $item->category,
                'region' => $item->region,
                'diamonds' => (string) $item->diamonds,
                'price' => (int) $price,
            ];
        });

        $displayProducts = $displayProducts->sortBy('price')->values();

        return view('admin.diamondpricemanager', [
            'products' => $displayProducts,
            'region' => 'global',
            'overrides' => $overrides,
            'isWwm' => true,
        ]);
    }

    public function fetchWwmFromApi(Request $request)
    {
        $result = $this->performWwmFetch();

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    private function performWwmFetch(): array
    {
        $baseUrl = $this->getEncryptedSetting('settings.api.mlbb_base_url_enc', '');
        if (trim($baseUrl) === '') {
            $msg = 'Please set MLBB API Base URL in Cookie/API Manager.';
            Cache::forever('wwm.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }
        $url = $this->getEncryptedSetting('settings.api.wwm_products_url_enc', '');
        if (trim($url) === '') {
            $msg = 'Please set WWM Products API URL in Cookie/API Manager.';
            Cache::forever('wwm.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }
        $url = $this->resolveUrl($baseUrl, $url);
        try {
            $resp = Http::timeout(12)->get($url);
            if (! $resp->successful()) {
                $msg = 'API Error: '.$resp->status();
                Cache::forever('wwm.status_message', $msg);
                return ['success' => false, 'message' => $msg];
            }
            $data = $resp->json();
        } catch (\Exception $e) {
            $msg = 'Fetch failed: '.$e->getMessage();
            Cache::forever('wwm.status_message', $msg);
            return ['success' => false, 'message' => $msg];
        }

        $count = 0;
        if (is_array($data)) {
            if (isset($data['data']) && is_array($data['data'])) {
                $data = $data['data'];
            } elseif (isset($data['items']) && is_array($data['items'])) {
                $data = $data['items'];
            }
            $normalizeKeys = function (array $item): array {
                $out = [];
                foreach ($item as $k => $v) {
                    if (is_string($k)) {
                        $out[strtolower($k)] = $v;
                    }
                }

                return $out;
            };

            $keys = array_keys($data);
            $isList = $keys === range(0, count($keys) - 1);
            if ($isList) {
                $data = ['Diamonds' => $data];
            }

            foreach ($data as $category => $items) {
                if (! is_array($items)) {
                    continue;
                }
                $categoryName = is_string($category) ? $category : 'Diamonds';
                $itemKeys = array_keys($items);
                $itemsIsList = $itemKeys === range(0, count($itemKeys) - 1);

                foreach ($items as $itemKey => $item) {
                    $pid = null;
                    $name = null;
                    $diamonds = 0;
                    $price = 0;

                    if (is_array($item)) {
                        $x = $normalizeKeys($item);
                        $pid = $x['product_id'] ?? $x['id'] ?? null;
                        if (! $pid && ! $itemsIsList) {
                            $pid = $itemKey;
                        }
                        $name = $x['name'] ?? $x['product_name'] ?? $x['title'] ?? null;
                        if (isset($x['price'])) {
                            $price = preg_replace('/[^\d]/', '', (string) $x['price']);
                        }
                        if (isset($x['diamonds'])) {
                            $diamonds = $x['diamonds'];
                        } elseif ($name && preg_match('/(\d+)\s*(diamond|diamonds|dm|E\s*B)/i', $name, $m)) {
                            $diamonds = $m[1];
                        }
                    } else {
                        if (! $itemsIsList) {
                            $pid = $itemKey;
                            $name = $item;
                        }
                    }

                    if ($pid) {
                        AdminWwmProduct::updateOrCreate(
                            ['product_id' => (string) $pid, 'region' => 'global'],
                            [
                                'name' => $name,
                                'diamonds' => (string) $diamonds,
                                'price' => (int) $price,
                                'category' => $categoryName,
                                'status' => 1,
                            ]
                        );
                        $count++;
                    }
                }
            }
        }

        Cache::forget('wwm.products');
        Cache::forever('wwm.status_message', 'Active');

        return ['success' => true, 'message' => 'Fetched '.$count.' WWM items.'];
    }

    public function updateWwmPrices(Request $request)
    {
        $updates = $request->input('updates', []);
        $count = 0;
        foreach ($updates as $u) {
            $pid = $u['product_id'] ?? null;
            $price = $u['price'] ?? null;

            if ($pid && $price !== null && is_numeric($price) && (float) $price >= 0) {
                $base = AdminWwmProduct::where('product_id', $pid)->first();
                $region = $base->region ?? 'global';

                $prefixedId = 'wwm_'.$pid;

                $ov = ProductPrice::updateOrCreate(
                    ['product_id' => $prefixedId, 'region' => $region],
                    [
                        'name' => $base->name ?? null,
                        'category' => $base->category ?? 'Diamonds',
                        'diamonds' => (string) ($base->diamonds ?? 0),
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );

                UserWwmProduct::updateOrCreate(
                    ['product_id' => (string) $pid, 'region' => $region],
                    [
                        'name' => $ov->name ?? ($base->name ?? null),
                        'category' => $ov->category ?? ($base->category ?? 'Diamonds'),
                        'diamonds' => (string) ($ov->diamonds ?? ($base->diamonds ?? 0)),
                        'price' => (int) $price,
                        'status' => 1,
                    ]
                );
                $count++;
            }
        }

        return back()->with('success', 'Updated '.$count.' prices.');
    }

    public function bulkUpdateWwmPrices(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric',
        ]);

        $percentage = (float) $request->input('percentage');
        $items = AdminWwmProduct::all();
        $count = 0;

        foreach ($items as $base) {
            $newPrice = $base->price * (1 + ($percentage / 100));
            $newPrice = (int) round($newPrice);

            $region = $base->region ?? 'global';
            $prefixedId = 'wwm_'.$base->product_id;

            $ov = ProductPrice::updateOrCreate(
                ['product_id' => $prefixedId, 'region' => $region],
                [
                    'name' => $base->name,
                    'category' => $base->category ?? 'Diamonds',
                    'diamonds' => (string) ($base->diamonds ?? 0),
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );

            UserWwmProduct::updateOrCreate(
                ['product_id' => (string) $base->product_id, 'region' => $region],
                [
                    'name' => $ov->name,
                    'category' => $ov->category ?? 'Diamonds',
                    'diamonds' => (string) ($ov->diamonds ?? ($base->diamonds ?? 0)),
                    'price' => $newPrice,
                    'status' => 1,
                ]
            );
            $count++;
        }

        return back()->with('success', "Updated $count WWM items by $percentage%.");
    }

    public function refreshCache(Request $request)
    {
        $results = [];

        // MLBB
        $results['mlbb'] = $this->performMlbbFetch('all');

        // PUBG
        $results['pubg'] = $this->performPubgFetch();

        // MCGG
        $results['mcgg'] = $this->performMcggFetch();

        // WWM
        $results['wwm'] = $this->performWwmFetch();

        $messages = [];
        foreach ($results as $key => $r) {
            $status = $r['success'] ? 'Success' : 'Failed';
            $messages[] = strtoupper($key) . ": $status";
        }

        return response()->json([
            'success' => true,
            'message' => implode(' | ', $messages),
            'details' => $results
        ]);
    }

    public function getCacheStatus(Request $request)
    {
        $status = [];
        
        $status['mlbb'] = [
            'key' => 'mlbb.products',
            'has_cache' => Cache::has('mlbb.products'),
            'status_message' => Cache::get('mlbb.status_message', 'Not Checked'),
            'db_count' => AdminMlProduct::count(),
            'last_updated' => AdminMlProduct::max('updated_at'),
        ];

        $status['pubg'] = [
            'key' => 'pubg.products',
            'has_cache' => Cache::has('pubg.products'),
            'status_message' => Cache::get('pubg.status_message', 'Not Checked'),
            'db_count' => AdminPubgProduct::count(),
            'last_updated' => AdminPubgProduct::max('updated_at'),
        ];

        $status['mcgg'] = [
            'key' => 'mcgg.products',
            'has_cache' => Cache::has('mcgg.products'),
            'status_message' => Cache::get('mcgg.status_message', 'Not Checked'),
            'db_count' => AdminMcggProduct::count(),
            'last_updated' => AdminMcggProduct::max('updated_at'),
        ];

        $status['wwm'] = [
            'key' => 'wwm.products',
            'has_cache' => Cache::has('wwm.products'),
            'status_message' => Cache::get('wwm.status_message', 'Not Checked'),
            'db_count' => AdminWwmProduct::count(),
            'last_updated' => AdminWwmProduct::max('updated_at'),
        ];

        return response()->json($status);
    }
}
