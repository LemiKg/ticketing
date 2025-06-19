<script setup lang="ts">
import Card from '@/Components/base/Card.vue';
import ContactCard from '@/Components/base/ContactCard.vue';
import ContactMeta from '@/Components/base/ContactMeta.vue';
import ContactForm from '@/Components/forms/ContactForm.vue';
import WebsiteLayout from '@/Layouts/WebsiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const hardwareFeatures = computed(() => [
  {
    icon: 'pi-box',
    title: t('website.hardware.cards.card1.title'),
    description: t('website.hardware.cards.card1.description'),
  },
  {
    icon: 'pi-id-card',
    title: t('website.hardware.cards.card2.title'),
    description: t('website.hardware.cards.card2.description'),
  },
  {
    icon: 'pi-wifi',
    title: t('website.hardware.cards.card3.title'),
    description: t('website.hardware.cards.card3.description'),
  },
  {
    icon: 'pi-lock',
    title: t('website.hardware.cards.card4.title'),
    description: t('website.hardware.cards.card4.description'),
  },
]);
const softwareFeatures = computed(() => [
  {
    icon: 'pi-clock',
    title: t('website.software.cards.card1.title'),
    description: t('website.software.cards.card1.description'),
  },
  {
    icon: 'pi-book',
    title: t('website.software.cards.card2.title'),
    description: t('website.software.cards.card2.description'),
  },
  {
    icon: 'pi-calendar',
    title: t('website.software.cards.card3.title'),
    description: t('website.software.cards.card3.description'),
  },
  {
    icon: 'pi-briefcase',
    title: t('website.software.cards.card4.title'),
    description: t('website.software.cards.card4.description'),
  },
  {
    icon: 'pi-lock',
    title: t('website.software.cards.card5.title'),
    description: t('website.software.cards.card5.description'),
  },
  {
    icon: 'pi-cloud',
    title: t('website.software.cards.card6.title'),
    description: t('website.software.cards.card6.description'),
  },
]);
const supportTiers = computed(() => [
  {
    icon: 'pi pi-headphones',
    title: t('website.support.cards.card1.title'),
    description: t('website.support.cards.card1.description'),
  },
  {
    icon: 'pi pi-star',
    title: t('website.support.cards.card2.title'),
    description: t('website.support.cards.card2.description'),
  },
]);
</script>

<template>
  <Head>
    <!-- Basic Meta Tags -->
    <title>Laravel Vue Boilerplate - Modern Web Application</title>
    <meta
      name="title"
      content="Laravel Vue Boilerplate - Modern Web Application"
    />
    <meta
      name="description"
      content="Modern Laravel + Vue.js application boilerplate with authentication, user management, and role-based access control."
    />
    <meta
      name="keywords"
      content="laravel, vue.js, boilerplate, web application, authentication, user management, modern stack"
    />
    <meta name="author" content="Laravel Vue Boilerplate" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#4f46e5" />
    <link rel="canonical" href="#" />

    <!-- Favicon (favicon.io generated files) -->
    <link rel="icon" type="image/x-icon" href="/images/favicon/favicon.ico" />
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="/images/favicon/favicon-32x32.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="/images/favicon/favicon-16x16.png"
    />
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="/images/favicon/apple-touch-icon.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="192x192"
      href="/images/favicon/android-chrome-192x192.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="512x512"
      href="/images/favicon/android-chrome-512x512.png"
    />
    <link rel="manifest" href="/images/favicon/site.webmanifest" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="#" />
    <meta
      property="og:title"
      content="Laravel Vue Boilerplate - Modern Web Application"
    />
    <meta
      property="og:description"
      content="Modern Laravel + Vue.js application boilerplate with authentication, user management, and role-based access control."
    />
    <meta property="og:image" content="/images/logo-light.webp" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:alt" content="Laravel Vue Boilerplate Logo" />
    <meta property="og:site_name" content="Laravel Vue Boilerplate" />
    <meta property="og:locale" content="en_US" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="#" />
    <meta
      name="twitter:title"
      content="Laravel Vue Boilerplate - Modern Web Application"
    />
    <meta
      name="twitter:description"
      content="Modern Laravel + Vue.js application boilerplate with authentication, user management, and role-based access control."
    />
    <meta name="twitter:image" content="/images/logo-light.webp" />
    <meta name="twitter:image:alt" content="Laravel Vue Boilerplate Logo" />

    <!-- Additional SEO and PWA -->
    <meta name="application-name" content="Laravel Vue Boilerplate" />
    <meta name="apple-mobile-web-app-title" content="Laravel Vue Boilerplate" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta
      name="apple-mobile-web-app-status-bar-style"
      content="black-translucent"
    />
    <meta name="mobile-web-app-capable" content="yes" />
  </Head>

  <WebsiteLayout>
    <div class="bg-gray-900">
      <!-- Hero Section -->
      <div class="relative overflow-hidden">
        <div class="absolute inset-0">
          <img
            src="/images/HeroSectionImage.webp"
            alt="Hero Background"
            class="h-full w-full object-cover opacity-40 dark:opacity-20"
          />
        </div>
        <div class="relative pt-20 pb-20 sm:pt-40 sm:pb-24">
          <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
              <h1
                class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl"
              >
                {{ t('website.hero.title') }}
                <span
                  class="from-primary-600 to-secondary-400 block bg-linear-to-r bg-clip-text text-transparent"
                >
                  {{ t('website.hero.title_span') }}
                </span>
              </h1>
              <p
                class="mx-auto mt-6 max-w-md text-lg text-gray-300 sm:text-xl md:mt-8 md:max-w-3xl"
              >
                {{ t('website.hero.description') }}
              </p>
              <div class="mt-8 flex justify-center gap-4">
                <Link
                  href="#pricing"
                  class="bg-primary-600 dark:bg-secondary-400 hover:bg-primary-700 dark:hover:bg-primary-400 rounded-xl px-8 py-4 font-extrabold text-gray-900 transition-colors duration-600"
                >
                  {{ t('website.hero.button') }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hardware & Software Overview -->
      <div id="features" class="bg-gray-100 py-20 dark:bg-gray-900">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mb-16 text-left">
            <h2
              class="text-4xl font-extrabold text-gray-900 sm:text-6xl dark:text-white"
            >
              {{ t('website.hardware.title') }}
            </h2>
            <p class="mt-4 text-gray-600 sm:text-2xl dark:text-gray-300">
              {{ t('website.hardware.description') }}
            </p>
          </div>
          <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
            <div>
              <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div
                  v-for="item in hardwareFeatures"
                  :key="item.title"
                  class="dark:border-primary-900 flex items-start gap-4 rounded-3xl border bg-white shadow-sm transition-shadow duration-300 hover:shadow-xl dark:bg-gray-800"
                >
                  <div>
                    <Card
                      :icon="item.icon"
                      :title="item.title"
                      :description="item.description"
                      class="flex flex-col items-start p-10 text-gray-900 dark:text-white"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div>
              <img
                src="/images/card_checkin.webp"
                alt="Check in with a card"
                class="h-full w-full rounded-3xl object-cover"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Key Features Section -->
      <div class="bg-gray-50 py-20 dark:bg-gray-800">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mb-16 text-left">
            <h2
              class="text-4xl font-extrabold text-gray-900 sm:text-6xl dark:text-white"
            >
              {{ t('website.software.title') }}
            </h2>
            <p class="mt-4 text-gray-600 sm:text-2xl dark:text-gray-300">
              {{ t('website.software.description') }}
            </p>
          </div>
          <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="item in softwareFeatures.slice(0, 6)"
              :key="item.title"
              class="dark:border-primary-900 rounded-3xl border bg-white shadow-sm transition-shadow duration-300 hover:shadow-xl dark:bg-gray-900"
            >
              <Card
                :icon="item.icon"
                :title="item.title"
                :description="item.description"
                class="flex min-w-[250px] flex-col items-start p-10 text-gray-900 dark:text-white"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing Preview -->
      <div id="pricing" class="bg-gray-100 py-20 dark:bg-gray-900">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mb-16 text-start">
            <h2
              class="text-4xl font-extrabold text-gray-900 sm:text-6xl dark:text-white"
            >
              {{ t('website.pricing.title') }}
            </h2>
            <p class="mt-4 text-gray-600 sm:text-2xl dark:text-gray-300">
              {{ t('website.pricing.description') }}
            </p>
          </div>
          <div
            class="grid justify-items-center gap-8 md:grid-cols-2 lg:grid-cols-3"
          >
            <!-- Standard -->
            <div
              class="dark:border-primary-900 flex h-full w-full flex-col items-start justify-between rounded-2xl bg-white p-8 shadow-xl dark:border dark:bg-gray-800"
            >
              <div class="w-full">
                <h3
                  class="mb-2 text-center text-3xl font-bold text-gray-900 dark:text-white"
                >
                  {{ t('website.pricing.cards.card1.title') }}
                </h3>
                <p class="mb-2 text-base font-light text-gray-400">
                  {{ t('website.pricing.cards.card1.employees') }}
                </p>
                <p
                  class="text-primary-600 dark:text-secondary-400 mb-2 text-xl font-semibold"
                >
                  {{ t('website.pricing.cards.card1.includes') }}
                </p>
                <ul class="mb-4 space-y-2">
                  <li
                    class="flex items-center text-gray-600 dark:text-gray-200"
                  >
                    {{ t('website.pricing.cards.card1.feature1') }}
                  </li>
                  <li
                    class="flex items-center text-gray-600 dark:text-gray-200"
                  >
                    {{ t('website.pricing.cards.card1.feature2') }}
                  </li>
                  <li
                    class="flex items-center text-gray-600 dark:text-gray-200"
                  >
                    {{ t('website.pricing.cards.card1.feature3') }}
                  </li>
                  <li
                    class="flex items-center text-gray-600 dark:text-gray-200"
                  >
                    {{ t('website.pricing.cards.card1.feature4') }}
                  </li>
                </ul>
              </div>
              <a
                href="#contact"
                class="bg-primary-600 dark:bg-secondary-400 hover:bg-primary-700 dark:hover:bg-primary-400 w-full rounded-lg px-4 py-2 text-center font-bold text-gray-900 transition-colors duration-600"
              >
                {{ t('website.pricing.cards.card1.button') }}
              </a>
            </div>

            <!-- Premium -->
            <div
              class="dark:border-primary-900 flex h-full w-full flex-col items-start justify-between rounded-2xl bg-white p-8 shadow-xl dark:border dark:bg-gray-800"
            >
              <div class="w-full">
                <h3
                  class="mb-2 text-center text-3xl font-bold text-gray-900 dark:text-white"
                >
                  {{ t('website.pricing.cards.card2.title') }}
                </h3>
                <p class="mb-2 text-base font-light text-gray-400">
                  {{ t('website.pricing.cards.card2.employees') }}
                </p>
                <p
                  class="text-primary-600 dark:text-secondary-400 mb-2 text-xl font-semibold"
                >
                  {{ t('website.pricing.cards.card2.includes') }}
                </p>
                <ul class="mb-4 space-y-2">
                  <li
                    class="flex items-center text-gray-600 dark:text-gray-200"
                  >
                    {{ t('website.pricing.cards.card2.feature1') }}
                  </li>
                  <li
                    class="flex items-center text-gray-600 dark:text-gray-200"
                  >
                    {{ t('website.pricing.cards.card2.feature2') }}
                  </li>
                </ul>
              </div>
              <a
                href="#contact"
                class="bg-primary-600 dark:bg-secondary-400 hover:bg-primary-700 dark:hover:bg-primary-400 w-full rounded-lg px-4 py-2 text-center font-bold text-gray-900 transition-colors duration-600"
              >
                {{ t('website.pricing.cards.card2.button') }}
              </a>
            </div>

            <!-- Enterprise -->
            <div
              class="dark:border-primary-900 flex h-full w-full flex-col items-start justify-between rounded-2xl bg-white p-8 shadow-xl dark:border dark:bg-gray-800"
            >
              <div>
                <h3
                  class="mb-2 text-center text-3xl font-bold text-gray-900 dark:text-white"
                >
                  {{ t('website.pricing.cards.card3.title') }}
                </h3>
                <p class="mb-2 text-base font-light text-gray-400">
                  {{ t('website.pricing.cards.card3.employees') }}
                </p>
                <p
                  class="text-primary-600 dark:text-secondary-400 mb-2 text-xl font-semibold"
                >
                  {{ t('website.pricing.cards.card3.includes') }}
                </p>
                <ul class="mb-4 space-y-2">
                  <li
                    class="flex items-center text-gray-600 dark:text-gray-200"
                  >
                    {{ t('website.pricing.cards.card3.feature1') }}
                  </li>
                </ul>
              </div>
              <a
                href="#contact"
                class="bg-primary-600 dark:bg-secondary-400 hover:bg-primary-700 dark:hover:bg-primary-400 w-full rounded-lg px-4 py-2 text-center font-bold text-gray-900 transition-colors duration-600"
              >
                {{ t('website.pricing.cards.card3.button') }}
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Support Highlight -->
      <div id="support" class="bg-gray-50 py-20 dark:bg-gray-800">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mb-16 text-start">
            <h2
              class="text-4xl font-extrabold text-gray-900 sm:text-6xl dark:text-white"
            >
              {{ t('website.support.title') }}
            </h2>
            <p class="mt-4 text-gray-600 sm:text-2xl dark:text-gray-300">
              {{ t('website.support.description') }}
            </p>
          </div>
          <div class="mx-auto grid max-w-3xl gap-8 md:grid-cols-2">
            <div
              v-for="tier in supportTiers"
              :key="tier.title"
              class="dark:border-primary-900 flex items-center gap-4 rounded-2xl border bg-white shadow-sm transition-shadow duration-300 hover:shadow-xl dark:bg-gray-900"
            >
              <Card
                :icon="tier.icon"
                :title="tier.title"
                :description="tier.description"
                class="flex flex-col items-start p-10 text-gray-900 dark:text-white"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Contact/CTA Section -->
      <div
        id="contact"
        class="from-primary-300 dark:from-primary-700 to-secondary-300 dark:to-secondary-700 relative bg-linear-to-r py-20"
      >
        <!-- Hero Section -->
        <div class="mx-auto max-w-6xl py-20">
          <div class="relative px-4 sm:px-6 lg:px-8">
            <div class="text-start">
              <h2
                class="text-4xl font-extrabold text-gray-900 sm:text-6xl dark:text-white"
              >
                {{ t('website.contact.title') }}
              </h2>
              <p class="mt-4 text-gray-600 sm:text-2xl dark:text-gray-300">
                {{ t('website.contact.description') }}
              </p>
            </div>
          </div>

          <!-- Main Contact Content -->
          <div class="relative mt-20">
            <div class="px-4 sm:px-6 lg:px-8">
              <!-- Contact Form Card -->
              <div
                class="relative overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900"
              >
                <div class="relative p-6 md:p-12">
                  <div class="grid gap-12 lg:grid-cols-2">
                    <ContactForm />
                    <div class="relative">
                      <div class="sticky top-8">
                        <ContactMeta />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Cards -->
          <div
            class="mt-12 grid gap-8 px-4 sm:grid-cols-2 sm:px-6 lg:grid-cols-3 lg:px-8"
          >
            <ContactCard
              icon="pi-envelope"
              title="Email"
              description="office@example.com"
              class="bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white"
            />
            <ContactCard
              icon="pi-users"
              :title="t('website.contact.partnerships')"
              description="sales@example.com"
              class="bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white"
            />
            <ContactCard
              icon="pi-question-circle"
              :title="t('website.contact.support')"
              description="support@example.com"
              class="bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white"
            />
          </div>
        </div>
      </div>
    </div>
  </WebsiteLayout>
</template>

<style scoped></style>
