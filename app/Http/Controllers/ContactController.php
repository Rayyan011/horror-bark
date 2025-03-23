<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create() 
    {
        return view('pages.contacts.create');
    }

    public function store(StoreContactRequest $request)
    {
        Contact::create($request->validated());

        // Optional: Email notification
        // Mail::to('your-email@example.com')->send(new ContactFormSubmitted($request->validated()));

        return redirect()
            ->route('contacts.create')
            ->with('success', 'Thank you for your inquiry! We will get back to you soon.');
    }
}
