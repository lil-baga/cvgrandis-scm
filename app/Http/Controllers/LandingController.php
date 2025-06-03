<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        return view('landing.index', compact('user'));
    }

    public function form()
    {
        return view('landing.form');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:orders,email',
            'phone_number' => 'required|string|max:20',
            'service' => 'required|string|in:neon,backdrop,interior,letter,event',
            'description' => 'nullable|string',
            'image_ref' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $order = new Order();
            $order->name = $request->input('name');
            $order->email = $request->input('email');
            $order->phone_number = $request->input('phone_number');
            $order->service = $request->input('service');
            $order->description = $request->input('description');

            if ($request->hasFile('image_ref')) {
                $file = $request->file('image_ref');
                if (is_array($file)) { 
                    $file = $file[0];
                }

                if ($file->isValid()) {
                    $order->image_ref = file_get_contents($file->getRealPath());
                    $order->original_filename = $file->getClientOriginalName();
                    $order->mime_type = $file->getClientMimeType();
                }
            } else {
                $order->image_ref = null;
            }

            $order->save();

            return redirect()->route('landing')
                ->with('success', 'Pemesanan Anda berhasil dikirim! Kami akan segera menghubungi Anda.');
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan order: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim pemesanan. Silakan coba lagi.')
                ->withInput();
        }
    }
}
