<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Mail\ContactSupportMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $attachmentPaths = [];

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Get file information before moving
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store in public/uploads/contact-attachments directory
                $file->move(public_path('uploads/contact-attachments'), $filename);

                // Store relative path and metadata
                $attachmentPaths[] = [
                    'original_name' => $originalName,
                    'stored_name' => $filename,
                    'path' => 'uploads/contact-attachments/' . $filename,
                    'size' => $fileSize,
                    'mime_type' => $mimeType
                ];
            }
        }

        // Create contact submission
        $submission = ContactSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
            'status' => 'pending'
        ]);

        // Prepare contact data for email
        $contactData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'attachments_count' => count($attachmentPaths)
        ];

        // Send email to support via SMTP
        try {
            $supportEmail = config('mail.from.address', env('MAIL_FROM_ADDRESS', 'support@trackflow.com'));

            // Also check for a specific support email in env
            $toEmail = env('SUPPORT_EMAIL', $supportEmail);

            Mail::to($toEmail)->send(new ContactSupportMail($contactData, $attachmentPaths));

            Log::info('Contact support email sent successfully', [
                'from' => $request->email,
                'to' => $toEmail,
                'subject' => $request->subject
            ]);
        } catch (\Exception $e) {
            // Log the error but don't fail the request - message is still saved to database
            Log::error('Failed to send contact support email: ' . $e->getMessage(), [
                'from' => $request->email,
                'subject' => $request->subject
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully! We\'ll get back to you within 24 hours.',
            'data' => $submission
        ], 201);
    }
}
