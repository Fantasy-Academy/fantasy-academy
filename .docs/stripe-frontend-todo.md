# Frontend Stripe Integration TODO

This document outlines what needs to be implemented in the Vue 3 frontend to complete the Stripe subscription integration.

## API Endpoints Available

The backend provides these endpoints:

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/subscription/status` | GET | Yes | Get current subscription status |
| `/api/subscription/checkout` | POST | Yes | Create checkout session, returns URL |
| `/api/subscription/portal` | POST | Yes | Create billing portal session, returns URL |
| `/api/me` | GET | Yes | User info with `isMember` and `membershipExpiresAt` |

## Implementation Checklist

### 1. API Service Layer

- [ ] Create `src/services/subscriptionApi.ts`:

```typescript
// Suggested implementation
interface SubscriptionStatus {
  isActive: boolean;
  status: string | null;
  planId: string | null;
  currentPeriodEnd: string | null;
  canceledAt: string | null;
  willCancelAtPeriodEnd: boolean;
}

interface CheckoutResponse {
  checkoutUrl: string;
  sessionId: string;
}

interface PortalResponse {
  portalUrl: string;
}

export const subscriptionApi = {
  getStatus: async (): Promise<SubscriptionStatus> => {
    const response = await api.get('/subscription/status');
    return response.data;
  },

  createCheckout: async (plan: 'monthly' | 'yearly'): Promise<CheckoutResponse> => {
    const response = await api.post('/subscription/checkout', { plan });
    return response.data;
  },

  createPortal: async (): Promise<PortalResponse> => {
    const response = await api.post('/subscription/portal', {});
    return response.data;
  },
};
```

### 2. Pinia Store

- [ ] Create `src/stores/subscription.ts` or extend existing user store:

```typescript
// Suggested store implementation
export const useSubscriptionStore = defineStore('subscription', {
  state: () => ({
    status: null as SubscriptionStatus | null,
    loading: false,
    error: null as string | null,
  }),

  getters: {
    isActive: (state) => state.status?.isActive ?? false,
    willCancel: (state) => state.status?.willCancelAtPeriodEnd ?? false,
  },

  actions: {
    async fetchStatus() {
      this.loading = true;
      try {
        this.status = await subscriptionApi.getStatus();
      } catch (e) {
        this.error = 'Failed to fetch subscription status';
      } finally {
        this.loading = false;
      }
    },

    async redirectToCheckout(plan: 'monthly' | 'yearly') {
      const { checkoutUrl } = await subscriptionApi.createCheckout(plan);
      window.location.href = checkoutUrl;
    },

    async redirectToPortal() {
      const { portalUrl } = await subscriptionApi.createPortal();
      window.location.href = portalUrl;
    },
  },
});
```

### 3. Routes

- [ ] Add routes in `src/router/index.ts`:

```typescript
// Subscription routes
{
  path: '/subscription',
  children: [
    {
      path: '',
      name: 'subscription',
      component: () => import('@/views/subscription/SubscriptionPage.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: 'success',
      name: 'subscription-success',
      component: () => import('@/views/subscription/SubscriptionSuccessPage.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: 'cancel',
      name: 'subscription-cancel',
      component: () => import('@/views/subscription/SubscriptionCancelPage.vue'),
      meta: { requiresAuth: true },
    },
  ],
}
```

### 4. Components

#### Subscription Page (`src/views/subscription/SubscriptionPage.vue`)

- [ ] Create main subscription/pricing page:
  - Display monthly and yearly pricing options
  - Show current subscription status if subscribed
  - "Subscribe" buttons that redirect to Stripe Checkout
  - "Manage Subscription" button for existing subscribers

```vue
<!-- Suggested structure -->
<template>
  <div class="subscription-page">
    <!-- If not subscribed: show pricing plans -->
    <template v-if="!isSubscribed">
      <h1>Choose Your Plan</h1>

      <div class="pricing-grid">
        <PricingCard
          title="Monthly"
          price="9.99"
          period="month"
          @select="subscribe('monthly')"
        />
        <PricingCard
          title="Yearly"
          price="99.90"
          period="year"
          badge="Save 17%"
          @select="subscribe('yearly')"
        />
      </div>
    </template>

    <!-- If subscribed: show current plan -->
    <template v-else>
      <h1>Your Subscription</h1>
      <SubscriptionDetails :status="subscriptionStatus" />
      <button @click="openPortal">Manage Subscription</button>
    </template>
  </div>
</template>
```

#### Success Page (`src/views/subscription/SubscriptionSuccessPage.vue`)

- [ ] Create success callback page:
  - Display success message
  - Optionally verify subscription via API
  - Link back to main app

```vue
<!-- Suggested structure -->
<template>
  <div class="success-page">
    <h1>Welcome to Fantasy Academy Premium!</h1>
    <p>Your subscription is now active.</p>
    <router-link to="/">Start Playing</router-link>
  </div>
</template>
```

#### Cancel Page (`src/views/subscription/SubscriptionCancelPage.vue`)

- [ ] Create cancel callback page:
  - Display message about incomplete checkout
  - Link back to subscription page to retry

#### Pricing Card Component (`src/components/subscription/PricingCard.vue`)

- [ ] Create reusable pricing card:
  - Title, price, period
  - Feature list
  - CTA button
  - Optional badge (e.g., "Best Value")

#### Subscription Details Component (`src/components/subscription/SubscriptionDetails.vue`)

- [ ] Show current subscription info:
  - Current plan
  - Next billing date
  - Cancellation status if applicable

### 5. User Info Updates

- [ ] Update user store/type to include:
  ```typescript
  interface User {
    // ... existing fields
    isMember: boolean;
    membershipExpiresAt: string | null;
  }
  ```

- [ ] Update `/api/me` response handling to include new fields

### 6. Feature Gating UI

- [ ] Create composable or component for feature gating:

```typescript
// src/composables/useMembership.ts
export function useMembership() {
  const userStore = useUserStore();

  const isMember = computed(() => userStore.user?.isMember ?? false);

  const requireMembership = (callback: () => void) => {
    if (isMember.value) {
      callback();
    } else {
      router.push('/subscription');
    }
  };

  return { isMember, requireMembership };
}
```

- [ ] Create upgrade prompt component for locked features:

```vue
<!-- src/components/subscription/UpgradePrompt.vue -->
<template>
  <div class="upgrade-prompt">
    <LockIcon />
    <p>This feature requires a premium subscription</p>
    <router-link to="/subscription">Upgrade Now</router-link>
  </div>
</template>
```

### 7. Navigation Updates

- [ ] Add subscription link to user menu/navigation
- [ ] Show membership badge for premium users
- [ ] Consider showing subscription status in header

### 8. Environment Variables

- [ ] Add to frontend `.env`:
```env
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_xxx
```

Note: The publishable key is only needed if you want to show pricing from Stripe directly. For the current implementation, the backend handles all Stripe interactions.

## Design Considerations

### Pricing Display

- Consider fetching prices from backend if you want dynamic pricing
- Current implementation uses lookup keys, prices are set in Stripe

### Loading States

- Show loading spinners during API calls
- Disable buttons during checkout/portal redirect
- Handle network errors gracefully

### Mobile Responsiveness

- Ensure pricing cards stack properly on mobile
- Test Stripe Checkout on mobile devices

## Testing Notes

### Test Cards (Stripe Test Mode)

| Number | Result |
|--------|--------|
| 4242 4242 4242 4242 | Success |
| 4000 0000 0000 0002 | Declined |
| 4000 0000 0000 3220 | 3D Secure required |

### Test Scenarios

1. New user subscribes (monthly)
2. New user subscribes (yearly)
3. User cancels subscription
4. User updates payment method
5. User with expired subscription tries to access premium features
6. User in trial period (if applicable)

## Notes

- Stripe Checkout handles all payment UI - no card input forms needed
- Webhook handles subscription creation - don't rely on success URL alone
- Consider polling `/api/subscription/status` after success redirect to verify
- Billing Portal handles all subscription management (cancel, update card, etc.)
