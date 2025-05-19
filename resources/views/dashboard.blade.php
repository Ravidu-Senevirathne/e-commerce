<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Welcome to Your Dashboard</h3>

                    @if(auth()->user()->isAdmin())
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                        <p>You have admin privileges. <a href="{{ route('admin.dashboard') }}" class="underline font-bold">Go to Admin Dashboard</a></p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-600 text-white rounded-lg shadow-md p-6">
                            <h5 class="text-lg font-bold mb-2">My Orders</h5>
                            <p class="text-xl mb-4">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                View your order history
                            </p>
                            <div class="flex justify-between items-center border-t border-blue-500 pt-3 mt-3">
                                <a class="text-white hover:underline" href="#">View Orders</a>
                                <div class="text-sm text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>

                        <div class="bg-green-600 text-white rounded-lg shadow-md p-6">
                            <h5 class="text-lg font-bold mb-2">My Profile</h5>
                            <p class="text-xl mb-4">
                                <i class="fas fa-user-circle mr-2"></i>
                                Update your account details
                            </p>
                            <div class="flex justify-between items-center border-t border-green-500 pt-3 mt-3">
                                <a class="text-white hover:underline" href="{{ route('profile.edit') }}">View Profile</a>
                                <div class="text-sm text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
