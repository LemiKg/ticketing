<script setup lang="ts">
import WebsiteLayout from '@/Layouts/WebsiteLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
  <Head>
    <title>Cookie Policy - TimeHive</title>
    <meta
      name="description"
      content="TimeHive Cookie Policy - Learn about how we use cookies and similar technologies to enhance your experience with our employee time tracking system."
    />
    <meta name="robots" content="index, follow" />
    <link rel="canonical" href="https://timehive.org/cookies" />
  </Head>

  <WebsiteLayout>
    <div class="bg-white py-20 dark:bg-gray-900">
      <div class="mx-auto max-w-4xl px-4 pt-10 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12 text-center">
          <h1 class="mb-4 text-4xl font-bold text-gray-900 dark:text-white">
            Cookie Policy
          </h1>
          <p class="text-lg text-gray-600 dark:text-gray-300">
            Last updated: {{ new Date().toLocaleDateString() }}
          </p>
        </div>

        <!-- Content -->
        <div class="prose prose-lg dark:prose-invert max-w-none">
          <!-- Introduction -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              1. Introduction
            </h2>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              This Cookie Policy explains how our application ("we," "our," or
              "us") uses cookies and similar tracking technologies when you
              visit our website and use our web application services.
            </p>
            <p class="leading-relaxed text-gray-700 dark:text-gray-300">
              By continuing to use our website and services, you consent to the
              use of cookies as described in this policy.
            </p>
          </section>

          <!-- What Are Cookies -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              2. What Are Cookies
            </h2>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              Cookies are small text files that are stored on your computer or
              mobile device when you visit a website. They allow the website to
              recognize your device and remember certain information about your
              preferences or past actions.
            </p>
            <p class="leading-relaxed text-gray-700 dark:text-gray-300">
              We use cookies to enhance your experience, provide personalized
              content, and analyze how our website and services are used.
            </p>
          </section>

          <!-- Types of Cookies -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              3. Types of Cookies We Use
            </h2>

            <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">
              3.1 Essential Cookies
            </h3>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              These cookies are necessary for the website to function properly
              and cannot be switched off. They include:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>Authentication cookies to keep you logged in</li>
              <li>Session cookies to maintain your current session</li>
              <li>
                Security cookies to protect against cross-site request forgery
              </li>
              <li>Load balancing cookies for proper system functionality</li>
            </ul>

            <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">
              3.2 Functional Cookies
            </h3>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              These cookies enhance functionality and personalization:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>Language preference cookies</li>
              <li>Theme preference cookies (light/dark mode)</li>
              <li>Dashboard layout preferences</li>
              <li>Time zone settings</li>
              <li>Remember user preferences for reports and filters</li>
            </ul>

            <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">
              3.3 Analytical Cookies
            </h3>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              These cookies help us understand how visitors interact with our
              website:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>Page view tracking</li>
              <li>Feature usage analytics</li>
              <li>Performance monitoring</li>
              <li>Error tracking and reporting</li>
              <li>User journey analysis</li>
            </ul>

            <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">
              3.4 Performance Cookies
            </h3>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              These cookies help us optimize the performance of our services:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>Caching preferences</li>
              <li>Load time optimization</li>
              <li>Resource loading preferences</li>
              <li>Connection quality assessment</li>
            </ul>
          </section>

          <!-- How We Use Cookies -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              4. How We Use Cookies
            </h2>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              We use cookies for the following purposes:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>
                <strong>Authentication:</strong> To keep you securely logged
                into your account
              </li>
              <li>
                <strong>Personalization:</strong> To remember your preferences
                and settings
              </li>
              <li>
                <strong>Security:</strong> To protect your account and prevent
                unauthorized access
              </li>
              <li>
                <strong>Performance:</strong> To optimize loading times and
                system responsiveness
              </li>
              <li>
                <strong>Analytics:</strong> To understand how our service is
                used and improve it
              </li>
              <li>
                <strong>Feature Enhancement:</strong> To provide advanced
                functionality like auto-save
              </li>
            </ul>
          </section>

          <!-- Managing Cookies -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              5. Managing Your Cookie Preferences
            </h2>

            <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">
              5.1 Browser Settings
            </h3>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              Most web browsers allow you to control cookies through their
              settings. You can:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>View and delete existing cookies</li>
              <li>Block or allow cookies from specific websites</li>
              <li>Block third-party cookies</li>
              <li>Clear all cookies when you close your browser</li>
              <li>Set up notifications when cookies are received</li>
            </ul>

            <h3 class="mb-3 text-xl font-medium text-gray-900 dark:text-white">
              5.2 Impact of Disabling Cookies
            </h3>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              Please note that disabling certain cookies may affect your ability
              to use TimeHive:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>You may not be able to stay logged in</li>
              <li>Your preferences may not be saved</li>
              <li>Some features may not work properly</li>
              <li>The website may load more slowly</li>
              <li>You may need to re-enter information repeatedly</li>
            </ul>
          </section>

          <!-- Third-Party Cookies -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              6. Third-Party Cookies
            </h2>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              We may use third-party services that set their own cookies:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>
                <strong>Analytics Services:</strong> To understand user behavior
                and improve our service
              </li>
              <li>
                <strong>Security Services:</strong> To protect against fraud and
                security threats
              </li>
              <li>
                <strong>Performance Monitoring:</strong> To monitor system
                performance and uptime
              </li>
              <li>
                <strong>Support Services:</strong> To provide customer support
                and help functionality
              </li>
            </ul>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              These third parties have their own privacy and cookie policies,
              which we encourage you to review.
            </p>
          </section>

          <!-- Data Retention -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              7. Cookie Retention Periods
            </h2>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              Different types of cookies are stored for different periods:
            </p>
            <ul class="mb-4 list-disc pl-6 text-gray-700 dark:text-gray-300">
              <li>
                <strong>Session Cookies:</strong> Deleted when you close your
                browser
              </li>
              <li>
                <strong>Authentication Cookies:</strong> Typically last 30 days
                or until logout
              </li>
              <li><strong>Preference Cookies:</strong> Usually last 1 year</li>
              <li>
                <strong>Analytics Cookies:</strong> Typically last 2 years
              </li>
              <li>
                <strong>Security Cookies:</strong> Vary based on security
                requirements
              </li>
            </ul>
          </section>

          <!-- Updates to Policy -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              8. Updates to This Policy
            </h2>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              We may update this Cookie Policy from time to time to reflect
              changes in our practices or for legal, operational, or regulatory
              reasons.
            </p>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              When we make significant changes, we will notify you by posting
              the updated policy on our website and updating the "Last updated"
              date at the top of this page.
            </p>
          </section>

          <!-- Contact Information -->
          <section
            class="mb-8 rounded-3xl bg-gray-100 px-4 py-6 dark:bg-gray-800"
          >
            <h2
              class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white"
            >
              9. Contact Us
            </h2>
            <p class="mb-4 leading-relaxed text-gray-700 dark:text-gray-300">
              If you have any questions about this Cookie Policy or our use of
              cookies, please contact us:
            </p>
            <div class="rounded-lg bg-white p-4 dark:bg-gray-700">
              <p class="mb-2 font-medium text-gray-900 dark:text-white">
                TimeHive Support Team
              </p>
              <p class="mb-2 text-gray-700 dark:text-gray-300">
                Email: support@timehive.tech
              </p>
              <p class="mb-2 text-gray-700 dark:text-gray-300">
                Website:
                <a
                  href="/#contact"
                  class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                  >Contact Form</a
                >
              </p>
            </div>
          </section>

          <!-- Consent Notice -->
          <section
            class="mb-8 rounded-3xl bg-blue-50 px-4 py-6 dark:bg-blue-900/20"
          >
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <svg
                  class="h-6 w-6 text-blue-600 dark:text-blue-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <h3
                  class="text-lg font-medium text-blue-900 dark:text-blue-100"
                >
                  Your Consent
                </h3>
                <p class="mt-2 text-blue-800 dark:text-blue-200">
                  By continuing to use TimeHive, you acknowledge that you have
                  read and understood this Cookie Policy and consent to the use
                  of cookies as described herein. You can withdraw your consent
                  at any time by adjusting your browser settings or contacting
                  us.
                </p>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </WebsiteLayout>
</template>
