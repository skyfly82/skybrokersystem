@extends('layouts.admin')

@section('title', 'General Settings')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                General Settings
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Configure general system settings
            </p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" name="company_name" id="company_name" value="SkyBrokerSystem" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700">Company Email</label>
                        <input type="email" name="company_email" id="company_email" value="contact@skybroker.pl" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700">Company Phone</label>
                        <input type="text" name="company_phone" id="company_phone" value="+48 123 456 789" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                        <select name="timezone" id="timezone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="Europe/Warsaw" selected>Europe/Warsaw</option>
                            <option value="Europe/London">Europe/London</option>
                            <option value="America/New_York">America/New_York</option>
                            <option value="Asia/Tokyo">Asia/Tokyo</option>
                       </select>
                   </div>

                   <div class="sm:col-span-2">
                       <label for="company_address" class="block text-sm font-medium text-gray-700">Company Address</label>
                       <textarea name="company_address" id="company_address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">ul. Przykładowa 123
00-001 Warszawa, Polska</textarea>
                   </div>

                   <div>
                       <label for="currency" class="block text-sm font-medium text-gray-700">Default Currency</label>
                       <select name="currency" id="currency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                           <option value="PLN" selected>PLN - Polish Złoty</option>
                           <option value="EUR">EUR - Euro</option>
                           <option value="USD">USD - US Dollar</option>
                       </select>
                   </div>

                   <div>
                       <label for="language" class="block text-sm font-medium text-gray-700">Default Language</label>
                       <select name="language" id="language" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                           <option value="pl" selected>Polski</option>
                           <option value="en">English</option>
                       </select>
                   </div>
               </div>

               <div class="mt-6 flex justify-end">
                   <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                       Cancel
                   </button>
                   <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                       Save Settings
                   </button>
               </div>
           </form>
       </div>
   </div>
</div>
@endsection
