<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        return view('bank-accounts.index');
    }

    public function create()
    {
        return view('bank-accounts.create');
    }

    public function store(Request $request)
    {
        // Handled via API
        return redirect()->route('bank-accounts.index')
            ->with('success', 'Created successfully');
    }

    public function edit($id)
    {
        return view('bank-accounts.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Handled via API
        return redirect()->route('bank-accounts.index')
            ->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        // Handled via API
        return redirect()->route('bank-accounts.index')
            ->with('success', 'Deleted successfully');
    }

    public function sync($id)
    {
        // Trigger sync via API
        return redirect()->route('bank-accounts.index')
            ->with('success', 'Sync initiated successfully');
    }


}