<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $contacts = Contact::all();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        // Set default icons/colors if not provided based on platform
        if (empty($data['icon'])) {
            $platform = strtolower($data['platform']);
            if (str_contains($platform, 'telegram')) {
                $data['icon'] = 'fab fa-telegram';
                if (empty($data['color'])) $data['color'] = '#0088cc';
            } elseif (str_contains($platform, 'facebook')) {
                $data['icon'] = 'fab fa-facebook';
                if (empty($data['color'])) $data['color'] = '#1877f2';
            } elseif (str_contains($platform, 'phone') || str_contains($platform, 'whatsapp')) {
                $data['icon'] = 'fas fa-phone';
                 if (empty($data['color'])) $data['color'] = '#25d366';
            } elseif (str_contains($platform, 'email')) {
                $data['icon'] = 'fas fa-envelope';
            }
        }

        Contact::create($data);

        return redirect()->back()->with('success', 'Contact added successfully.');
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        
        $request->validate([
            'platform' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $contact->update($request->except(['_token', '_method']));

        return redirect()->back()->with('success', 'Contact updated successfully.');
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->back()->with('success', 'Contact deleted successfully.');
    }
}
