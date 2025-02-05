<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen bg-gray-100">
      <!-- Navbar -->
      <nav class="bg-white shadow-md">
        <div class="container mx-auto flex justify-between items-center p-4">
          <h1 class="text-2xl font-bold text-blue-600">MyApp</h1>
          <div>
            <!-- <Link :href="route('login')" class="text-blue-600 font-semibold hover:underline">Login</Link> -->
            <Link :href="route('register')" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Register
            </Link>
          </div>
        </div>
      </nav>

      <!-- Hero Section -->
      <header class="text-center py-20 bg-blue-500 text-white">
        <h2 class="text-4xl font-bold">Welcome to MyApp</h2>
        <p class="mt-2 text-lg">Your one-stop solution for amazing features.</p>
        <router-link to="/register" class="mt-6 inline-block px-6 py-3 bg-white text-blue-600 font-semibold rounded-md hover:bg-gray-200">
          Get Started
        </router-link>
      </header>

      <!-- Features Section -->
      <section class="container mx-auto my-16 px-6 grid md:grid-cols-3 gap-8 text-center">
        <div class="p-6 bg-white shadow-md rounded-lg">
          <h3 class="text-xl font-semibold">Fast & Secure</h3>
          <p class="text-gray-600 mt-2">Experience blazing fast performance with top-notch security.</p>
        </div>
        <div class="p-6 bg-white shadow-md rounded-lg">
          <h3 class="text-xl font-semibold">Easy to Use</h3>
          <p class="text-gray-600 mt-2">A simple interface that anyone can use.</p>
        </div>
        <div class="p-6 bg-white shadow-md rounded-lg">
          <h3 class="text-xl font-semibold">24/7 Support</h3>
          <p class="text-gray-600 mt-2">Our team is available round the clock for assistance.</p>
        </div>
      </section>

      <!-- Footer -->
      <footer class="bg-gray-800 text-white text-center py-4">
        <p>&copy; 2025 MyApp. All rights reserved.</p>
      </footer>
    </div>
  </template>
