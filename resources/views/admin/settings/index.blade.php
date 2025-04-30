@extends('layouts.admin')

@section('title', 'Admin Settings')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h2 class="text-3xl font-bold mb-8 text-indigo-700 text-center">Admin Settings</h2>

    @if(session('message'))
        <div class="mb-6 px-4 py-3 bg-green-100 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-10">
        @csrf

        {{-- DaisySMS Configuration --}}
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-2xl font-semibold text-indigo-600 mb-4">DaisySMS Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="additional_price" class="block font-medium mb-1">SHOPVIACLONE GAIN (₦)</label>
                    <input type="number" step="0.01" name="additional_price" id="additional_price"
                        value="{{ old('additional_price', $additionalPrice) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div>
                    <label for="conversion_rate" class="block font-medium mb-1">Dollar to Naira Rate (₦)</label>
                    <input type="number" step="0.01" name="conversion_rate" id="conversion_rate"
                        value="{{ old('conversion_rate', $conversionRate) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div>
                    <label for="number_conversion_rate" class="block font-medium mb-1">Number USD to Naira Rate (₦)</label>
                    <input type="number" step="0.01" name="number_conversion_rate" id="number_conversion_rate"
                        value="{{ old('number_conversion_rate', $numberConversionRate) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div>
                    <label for="number_additional_price" class="block font-medium mb-1">Number Extra Gain (₦)</label>
                    <input type="number" step="0.01" name="number_additional_price" id="number_additional_price"
                        value="{{ old('number_additional_price', $numberAdditionalPrice) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label for="daisy_api_key" class="block font-medium mb-1">DaisySMS API Key</label>
                    <input type="text" name="daisy_api_key" id="daisy_api_key"
                        value="{{ old('daisy_api_key', $daisyApiKey) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>
            </div>
        </div>

        {{-- Tellabot Configuration --}}
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-2xl font-semibold text-indigo-600 mb-4">Tellabot Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tellabot_user" class="block font-medium mb-1">Tellabot User</label>
                    <input type="text" name="tellabot_user" id="tellabot_user"
                        value="{{ old('tellabot_user', $tellabot_user ?? '') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500"
                        required>
                </div>

                <div>
                    <label for="tellabot_api_key" class="block font-medium mb-1">Tellabot API Key</label>
                    <input type="text" name="tellabot_api_key" id="tellabot_api_key"
                        value="{{ old('tellabot_api_key', $tellabot_api_key ?? '') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500"
                        required>
                </div>

                <div>
                    <label class="block font-medium mb-1">Tellabot Conversion Rate (₦)</label>
                    <input type="number" step="0.01" name="tellabot_conversion_rate"
                        value="{{ $tellabot_conversion_rate }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-medium mb-1">Tellabot Markup (Others)</label>
                    <input type="number" step="0.01" name="tellabot_markup_default"
                        value="{{ $tellabot_markup_default }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-medium mb-1">Tellabot WhatsApp Markup (₦)</label>
                    <input type="number" step="0.01" name="tellabot_markup_whatsapp"
                        value="{{ $tellabot_markup_whatsapp }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-medium mb-1">Tellabot Telegram Markup (₦)</label>
                    <input type="number" step="0.01" name="tellabot_markup_telegram"
                        value="{{ $tellabot_markup_telegram }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-medium mb-1">Tellabot WhatsApp Price (₦)</label>
                    <input type="number" step="0.01" name="tellabot_whatsapp_price"
                        value="{{ old('tellabot_whatsapp_price', $tellabot_whatsapp_price ?? 150) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500" required>
                </div>

                <div>
                    <label class="block font-medium mb-1">Tellabot Telegram Price (₦)</label>
                    <input type="number" step="0.01" name="tellabot_telegram_price"
                        value="{{ old('tellabot_telegram_price', $tellabot_telegram_price ?? 100) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500" required>
                </div>

                <div>
                    <label class="block font-medium mb-1">Tellabot Other Services Gain (₦)</label>
                    <input type="number" step="0.01" name="tellabot_other_gain"
                        value="{{ old('tellabot_other_gain', $tellabot_other_gain ?? 0) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-indigo-500" required>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg shadow">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
