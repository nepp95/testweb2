<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessage;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ContactMessageController extends Controller
{
    /**
     * The error shown when the message could not be delivered.
     */
    private const string DELIVERY_FAILED = 'Je bericht kon niet worden verstuurd. Probeer het later opnieuw, of bel of mail ons.';

    /**
     * E-mail the contact form message to the foundation. Messages are never stored.
     */
    public function store(StoreContactMessageRequest $request, SiteSettings $settings): RedirectResponse
    {
        $redirect = redirect()->to(route('home').'#contact-form');

        if ($request->isLikelySpam()) {
            return $redirect->with('contact-status', 'sent');
        }

        $recipient = $settings->formRecipient();

        if ($recipient === null) {
            Log::error('Contact form message not sent: no valid recipient address is configured.');

            return $redirect->withInput()->withErrors(['form' => self::DELIVERY_FAILED]);
        }

        try {
            Mail::to($recipient)->send(new ContactMessage(
                naam: $request->validated('naam'),
                contact: $request->validated('contact'),
                onderwerp: $request->validated('onderwerp'),
                bericht: $request->validated('bericht'),
            ));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return $redirect->withInput()->withErrors(['form' => self::DELIVERY_FAILED]);
        }

        return $redirect->with('contact-status', 'sent');
    }
}
