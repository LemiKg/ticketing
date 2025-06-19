<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
  public function submit(Request $request)
  {
    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'message' => 'required|string|max:5000',
    ]);

    try {
      // Send email to your desired address
      Mail::to('example@gmail.com')->send(new ContactFormMail($validatedData));

      // Optionally send a confirmation email to the sender
      // Mail::to($validatedData['email'])->send(new ContactConfirmationMail($validatedData));

      return response()->json([
        'success' => true,
        'message' => 'Your message has been sent successfully!',
      ]);
    } catch (\Exception $e) {
      return response()->json(
        [
          'success' => false,
          'message' =>
            'Sorry, there was an error sending your message. Please try again.',
        ],
        500
      );
    }
  }
}
