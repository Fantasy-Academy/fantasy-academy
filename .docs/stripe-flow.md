# Stripe Subscription Integration Flow

## Overview

Fantasy Academy uses Stripe Checkout (hosted) for subscription payments. This approach eliminates PCI compliance requirements as all card handling happens on Stripe's servers.

## Architecture

### Key Components

```
┌─────────────────────────────────────────────────────────────────────────┐
│                              Frontend                                    │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │  Subscription   │  │  Checkout       │  │  Billing Portal │         │
│  │  Status Page    │  │  Redirect       │  │  Redirect       │         │
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘         │
└───────────┼────────────────────┼────────────────────┼───────────────────┘
            │                    │                    │
            ▼                    ▼                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                              API                                         │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │ GET /api/       │  │ POST /api/      │  │ POST /api/      │         │
│  │ subscription/   │  │ subscription/   │  │ subscription/   │         │
│  │ status          │  │ checkout        │  │ portal          │         │
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘         │
│           │                    │                    │                   │
│           ▼                    ▼                    ▼                   │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                    SubscriptionChecker                          │   │
│  │                    StripeClient                                  │   │
│  │                    SubscriptionRepository                        │   │
│  └─────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         Stripe API                                       │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │  Customers      │  │  Checkout       │  │  Billing Portal │         │
│  │                 │  │  Sessions       │  │  Sessions       │         │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘         │
└─────────────────────────────────────────────────────────────────────────┘
```

### Webhook Flow

```
┌─────────────┐         ┌─────────────┐         ┌─────────────────────┐
│   Stripe    │────────▶│  Webhook    │────────▶│  Message Handler    │
│   Event     │         │  Controller │         │                     │
└─────────────┘         └─────────────┘         └─────────────────────┘
                              │                          │
                              ▼                          ▼
                        ┌─────────────┐         ┌─────────────────────┐
                        │  Signature  │         │  Database Update    │
                        │  Verify     │         │  (Subscription)     │
                        └─────────────┘         └─────────────────────┘
```

## API Endpoints

### GET /api/subscription/status

Returns the current user's subscription status.

**Authentication**: Required (JWT)

**Response**:
```json
{
  "isActive": true,
  "status": "active",
  "planId": "price_xxx",
  "currentPeriodEnd": "2025-07-15T00:00:00+00:00",
  "canceledAt": null,
  "willCancelAtPeriodEnd": false
}
```

**Possible status values**:
- `active` - Subscription is active
- `canceled` - Subscription has been canceled
- `past_due` - Payment failed, subscription at risk
- `trialing` - In trial period (if enabled)

---

### POST /api/subscription/checkout

Creates a Stripe Checkout Session and returns the URL for redirecting the user.

**Authentication**: Required (JWT)

**Request**:
```json
{
  "plan": "monthly",
  "successUrl": "https://app.example.com/subscription/success?session_id={CHECKOUT_SESSION_ID}",
  "cancelUrl": "https://app.example.com/subscription/cancel"
}
```

**Parameters**:
- `plan` (required): `"monthly"` or `"yearly"`
- `successUrl` (optional): Where to redirect after successful payment. Use `{CHECKOUT_SESSION_ID}` as a literal placeholder - Stripe automatically replaces it with the actual session ID when redirecting.
- `cancelUrl` (optional): Where to redirect if user cancels

**Response**:
```json
{
  "checkoutUrl": "https://checkout.stripe.com/c/pay/cs_xxx",
  "sessionId": "cs_xxx"
}
```

**Flow**:
1. API creates/reuses Stripe Customer for the user
2. API looks up the price by lookup key (`fantasy_academy_monthly` or `fantasy_academy_yearly`)
3. API creates Checkout Session
4. Frontend redirects user to `checkoutUrl`
5. User completes payment on Stripe
6. Stripe redirects to `successUrl`
7. Stripe sends webhook event (handled asynchronously)

---

### POST /api/subscription/portal

Creates a Stripe Billing Portal session for subscription management.

**Authentication**: Required (JWT)

**Request**:
```json
{
  "returnUrl": "https://app.example.com/account"
}
```

**Parameters**:
- `returnUrl` (optional): Where to return after portal actions

**Response**:
```json
{
  "portalUrl": "https://billing.stripe.com/session/xxx"
}
```

**Note**: User must have a Stripe Customer ID (created during checkout).

---

### GET /api/me

Returns logged user info including membership status.

**Authentication**: Required (JWT)

**Response** (relevant fields):
```json
{
  "id": "uuid",
  "email": "user@example.com",
  "isMember": true,
  "membershipExpiresAt": "2025-07-15T00:00:00+00:00"
}
```

## Webhook Events

### Endpoint: POST /api/webhooks/stripe

**Authentication**: None (secured by signature verification)

### Handled Events

| Event Type | Handler | Action |
|------------|---------|--------|
| `checkout.session.completed` | `HandleCheckoutCompletedHandler` | Creates Subscription entity, links to User |
| `customer.subscription.updated` | `HandleSubscriptionUpdatedHandler` | Updates status, period dates, cancelation info |
| `customer.subscription.deleted` | `HandleSubscriptionDeletedHandler` | Marks subscription as canceled |

### Webhook Security

1. Stripe signs each webhook with your webhook secret
2. `WebhookVerifier` validates the signature before processing
3. Invalid signatures return 400 Bad Request
4. Webhook endpoint is excluded from JWT authentication

## User Subscription Journey

### New Subscription

```
1. User clicks "Subscribe" on frontend
2. Frontend calls POST /api/subscription/checkout
3. API returns checkoutUrl
4. Frontend redirects to Stripe Checkout
5. User enters payment details on Stripe
6. Stripe processes payment
7. Stripe redirects to successUrl
8. Stripe sends checkout.session.completed webhook
9. API creates Subscription entity
10. User is now a member
```

### Cancel Subscription

```
1. User clicks "Manage Subscription" on frontend
2. Frontend calls POST /api/subscription/portal
3. API returns portalUrl
4. Frontend redirects to Stripe Portal
5. User clicks "Cancel Subscription"
6. Stripe sends customer.subscription.updated webhook (cancel_at_period_end: true)
7. API updates subscription with canceledAt and cancelAtPeriodEnd
8. User remains active until period ends
9. At period end, Stripe sends customer.subscription.deleted webhook
10. API marks subscription as canceled
```

### Update Payment Method

```
1. User clicks "Manage Subscription" on frontend
2. Frontend redirects to Stripe Portal (via /api/subscription/portal)
3. User updates payment method in Stripe Portal
4. No webhook needed - Stripe handles internally
```

## Environment Configuration

### Required Environment Variables

```env
STRIPE_SECRET_KEY=sk_test_xxx       # Stripe secret key
STRIPE_PUBLISHABLE_KEY=pk_test_xxx  # Stripe publishable key (for frontend)
STRIPE_WEBHOOK_SECRET=whsec_xxx     # Webhook endpoint secret
FRONTEND_URI=http://localhost:5173  # Frontend URL for redirects
```

### Stripe Product Setup

Run the initialization command after setting up Stripe keys:

```bash
docker compose exec api bin/console app:stripe:init
```

This creates:
- Product: "Fantasy Academy Membership" with metadata `app: fantasy-academy`
- Monthly price with lookup key `fantasy_academy_monthly`
- Yearly price with lookup key `fantasy_academy_yearly`

## Testing

### Local Development with Stripe CLI

```bash
# Install Stripe CLI
brew install stripe/stripe-cli/stripe

# Login
stripe login

# Forward webhooks to local server
stripe listen --forward-to localhost:8080/api/webhooks/stripe

# In another terminal, trigger test events
stripe trigger checkout.session.completed
stripe trigger customer.subscription.updated
stripe trigger customer.subscription.deleted
```

### Test Users (Fixtures)

| Email | Has Subscription | Status |
|-------|-----------------|--------|
| admin@example.com | No | - |
| user@example.com | Yes | Active |
| user3@example.com | Yes | Canceled but in period |
| user4@example.com | Yes | Expired |

## Database Schema

### Subscription Entity

```sql
CREATE TABLE subscription (
    id UUID PRIMARY KEY,
    user_id UUID REFERENCES "user"(id),
    stripe_subscription_id VARCHAR(255) UNIQUE,
    stripe_customer_id VARCHAR(255),
    status VARCHAR(50),
    plan_id VARCHAR(255),
    current_period_start TIMESTAMP,
    current_period_end TIMESTAMP,
    canceled_at TIMESTAMP,
    cancel_at_period_end BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### User Entity (modified)

```sql
ALTER TABLE "user" ADD COLUMN stripe_customer_id VARCHAR(255);
```

## Service Classes

| Service | Responsibility |
|---------|---------------|
| `StripeClientInterface` | Abstraction for Stripe API calls |
| `StripeClient` | Implements Stripe API calls |
| `WebhookVerifierInterface` | Abstraction for webhook signature verification |
| `WebhookVerifier` | Verifies Stripe webhook signatures |
| `SubscriptionChecker` | Checks if user has active subscription |
| `SubscriptionRepository` | Database queries for subscriptions |

## Error Handling

| Scenario | Response |
|----------|----------|
| Invalid webhook signature | 400 Bad Request |
| User not authenticated | 401 Unauthorized |
| User has no Stripe customer (for portal) | 400 Bad Request |
| Price lookup key not found | 500 Internal Server Error |
| Unknown webhook event type | 200 OK (graceful handling) |
