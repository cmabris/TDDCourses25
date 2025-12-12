<?php

use Carbon\Carbon;
use Spatie\WebhookClient\Models\WebhookCall;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertSame;

it('can create a valid Paddle webhook signature', function () {
    // Arrange
    $originalTimestamp = 1718139311;
    [$originalArrBody, $originalSigHeader, $originalRawJsonBody] = getValidPaddleWebhookRequest();

    // Assert
    [$body, $header] = generateValidSignedPaddleWebhookRequest($originalArrBody, $originalTimestamp);
    assertSame(json_encode($body), $originalRawJsonBody);

    assertSame($originalSigHeader, $header);
});

it('stores a paddle purchase request', function () {
    // Arrange
    assertDatabaseCount(WebhookCall::class, 0);
    [$arrData] = getValidPaddleWebhookRequest();

    // We will have to generate a fresh signature because the timestamp cannot be older
    // than 5 seconds, or our webhook signature validator middleware will block the request
    [$requestBody, $requestHeaders] = generateValidSignedPaddleWebhookRequest($arrData);

    // Act
    // needed to prevent the checkout url slashes from being escaped
    // postJson will encode the JSON the same way as json_encode() without flags
    postJson('webhooks', $requestBody, $requestHeaders);

    // Assert
    assertDatabaseCount(WebhookCall::class, 1);
});

it('does not store invalid paddle purchase request', function () {
    // withoutExceptionHandling();
    // Arrange
    assertDatabaseCount(WebhookCall::class, 0);

    // Act
    post('webhooks', []);
    // Assert
    assertDatabaseCount(WebhookCall::class, 0);
});

function generateValidSignedPaddleWebhookRequest(array $data, ?int $timestamp = null): array
{
    $ts = $timestamp ?? Carbon::now()->unix();

    $secret = config('services.paddle.notification-endpoint-secret-key');
    // needed to prevent the checkout url slashes from being escaped
    // Use the same JSON encoding as postJson (without JSON_UNESCAPED_SLASHES)
    // because postJson will encode it this way
    $rawJsonBody = json_encode($data);

    $calculatedSig = hash_hmac('sha256', "{$ts}:{$rawJsonBody}", $secret);

    $header = ['Paddle-Signature' => "ts={$ts};h1={$calculatedSig}"];

    return [$data, $header];
}

function getValidPaddleWebhookRequest(): array
{
    $sigHeader = ['Paddle-Signature' => 'ts=1718139311;h1=548bd073a75ffa14243f411bf543ef76444db6f386f42e3b01342d1c3845db29'];

    $parsedData = [
        "event_id" => "evt_01j49ynfqw9ze2bgcbf5dk5k50",
        "event_type" => "transaction.completed",
        "occurred_at" => "2024-08-02T16:27:17.116352Z",
        "notification_id" => "ntf_01j49ynfvp5mz7mkvr92v4w5v7",
        "data" => [
            "id" => "txn_01j49ym2cj0542kgdpvcp3dkh6",
            "items" => [
                [
                    "price" => [
                        "id" => "pri_01j44a0y5gcrcxmybztgqk4zcy",
                        "name" => "pago producto tres",
                        "type" => "standard",
                        "status" => "active",
                        "quantity" => ["maximum" => 10000, "minimum" => 1],
                        "tax_mode" => "account_setting",
                        "created_at" => "2024-07-31T11:50:20.080991Z",
                        "product_id" => "pro_01j449zzjk223yrbnfp3aaqn84",
                        "unit_price" => [
                            "amount" => "2500",
                            "currency_code" => "EUR",
                        ],
                        "updated_at" => "2024-07-31T11:50:20.080992Z",
                        "custom_data" => null,
                        "description" => "pago unico tres",
                        "trial_period" => null,
                        "billing_cycle" => null,
                        "unit_price_overrides" => [],
                    ],
                    "price_id" => "pri_01j44a0y5gcrcxmybztgqk4zcy",
                    "quantity" => 1,
                    "proration" => null,
                ],
            ],
            "origin" => "web",
            "status" => "completed",
            "details" => [
                "totals" => [
                    "fee" => "171",
                    "tax" => "434",
                    "total" => "2500",
                    "credit" => "0",
                    "balance" => "0",
                    "discount" => "0",
                    "earnings" => "1895",
                    "subtotal" => "2066",
                    "grand_total" => "2500",
                    "currency_code" => "EUR",
                    "credit_to_balance" => "0",
                ],
                "line_items" => [
                    [
                        "id" => "txnitm_01j49ymsny3w8vxzjbc0frgh6k",
                        "totals" => [
                            "tax" => "434",
                            "total" => "2500",
                            "discount" => "0",
                            "subtotal" => "2066",
                        ],
                        "item_id" => null,
                        "product" => [
                            "id" => "pro_01j449zzjk223yrbnfp3aaqn84",
                            "name" => "TDD The Laravel Way",
                            "type" => "standard",
                            "status" => "active",
                            "image_url" => null,
                            "created_at" => "2024-07-31T11:49:48.755Z",
                            "updated_at" => "2024-08-02T16:04:52.053Z",
                            "custom_data" => ["product" => "three"],
                            "description" => "TDD The Laravel Way",
                            "tax_category" => "standard",
                        ],
                        "price_id" => "pri_01j44a0y5gcrcxmybztgqk4zcy",
                        "quantity" => 1,
                        "tax_rate" => "0.21",
                        "unit_totals" => [
                            "tax" => "434",
                            "total" => "2500",
                            "discount" => "0",
                            "subtotal" => "2066",
                        ],
                    ],
                ],
                "payout_totals" => [
                    "fee" => "183",
                    "tax" => "463",
                    "total" => "2672",
                    "credit" => "0",
                    "balance" => "0",
                    "discount" => "0",
                    "earnings" => "2026",
                    "fee_rate" => "0.05",
                    "subtotal" => "2209",
                    "grand_total" => "2672",
                    "currency_code" => "USD",
                    "exchange_rate" => "1.0688958",
                    "credit_to_balance" => "0",
                ],
                "tax_rates_used" => [
                    [
                        "totals" => [
                            "tax" => "434",
                            "total" => "2500",
                            "discount" => "0",
                            "subtotal" => "2066",
                        ],
                        "tax_rate" => "0.21",
                    ],
                ],
                "adjusted_totals" => [
                    "fee" => "171",
                    "tax" => "434",
                    "total" => "2500",
                    "earnings" => "1895",
                    "subtotal" => "2066",
                    "grand_total" => "2500",
                    "currency_code" => "EUR",
                ],
            ],
            "checkout" => [
                "url" => "https://localhost?_ptxn=txn_01j49ym2cj0542kgdpvcp3dkh6",
            ],
            "payments" => [
                [
                    "amount" => "2500",
                    "status" => "captured",
                    "created_at" => "2024-08-02T16:27:12.863511Z",
                    "error_code" => null,
                    "captured_at" => "2024-08-02T16:27:15.362606Z",
                    "method_details" => [
                        "card" => [
                            "type" => "visa",
                            "last4" => "4242",
                            "expiry_year" => 2025,
                            "expiry_month" => 10,
                            "cardholder_name" => "Carlos Abrisqueta",
                        ],
                        "type" => "card",
                    ],
                    "payment_method_id" => "paymtd_01j49ynbj9g5bzmesmmjez0gv4",
                    "payment_attempt_id" => "35d34015-64cf-44b6-9f0e-be12448843ef",
                    "stored_payment_method_id" =>
                        "7b58c622-17cb-4ace-8a98-7f0224f7f610",
                ],
            ],
            "billed_at" => "2024-08-02T16:27:15.743194Z",
            "address_id" => "add_01j49ymsdc57xvjzarpkdr3epk",
            "created_at" => "2024-08-02T16:26:30.762648Z",
            "invoice_id" => "inv_01j49ynerdrn6r0qrs0p882z4y",
            "updated_at" => "2024-08-02T16:27:16.669507499Z",
            "business_id" => null,
            "custom_data" => null,
            "customer_id" => "ctm_01j490w0zypkt01gx4w95mvmm5",
            "discount_id" => null,
            "receipt_data" => null,
            "currency_code" => "EUR",
            "billing_period" => null,
            "invoice_number" => "8169-10005",
            "billing_details" => null,
            "collection_mode" => "automatic",
            "subscription_id" => null,
        ],
    ];
    $rawJsonBody = '{"event_id":"evt_01j49ynfqw9ze2bgcbf5dk5k50","event_type":"transaction.completed","occurred_at":"2024-08-02T16:27:17.116352Z","notification_id":"ntf_01j49ynfvp5mz7mkvr92v4w5v7","data":{"id":"txn_01j49ym2cj0542kgdpvcp3dkh6","items":[{"price":{"id":"pri_01j44a0y5gcrcxmybztgqk4zcy","name":"pago producto tres","type":"standard","status":"active","quantity":{"maximum":10000,"minimum":1},"tax_mode":"account_setting","created_at":"2024-07-31T11:50:20.080991Z","product_id":"pro_01j449zzjk223yrbnfp3aaqn84","unit_price":{"amount":"2500","currency_code":"EUR"},"updated_at":"2024-07-31T11:50:20.080992Z","custom_data":null,"description":"pago unico tres","trial_period":null,"billing_cycle":null,"unit_price_overrides":[]},"price_id":"pri_01j44a0y5gcrcxmybztgqk4zcy","quantity":1,"proration":null}],"origin":"web","status":"completed","details":{"totals":{"fee":"171","tax":"434","total":"2500","credit":"0","balance":"0","discount":"0","earnings":"1895","subtotal":"2066","grand_total":"2500","currency_code":"EUR","credit_to_balance":"0"},"line_items":[{"id":"txnitm_01j49ymsny3w8vxzjbc0frgh6k","totals":{"tax":"434","total":"2500","discount":"0","subtotal":"2066"},"item_id":null,"product":{"id":"pro_01j449zzjk223yrbnfp3aaqn84","name":"TDD The Laravel Way","type":"standard","status":"active","image_url":null,"created_at":"2024-07-31T11:49:48.755Z","updated_at":"2024-08-02T16:04:52.053Z","custom_data":{"product":"three"},"description":"TDD The Laravel Way","tax_category":"standard"},"price_id":"pri_01j44a0y5gcrcxmybztgqk4zcy","quantity":1,"tax_rate":"0.21","unit_totals":{"tax":"434","total":"2500","discount":"0","subtotal":"2066"}}],"payout_totals":{"fee":"183","tax":"463","total":"2672","credit":"0","balance":"0","discount":"0","earnings":"2026","fee_rate":"0.05","subtotal":"2209","grand_total":"2672","currency_code":"USD","exchange_rate":"1.0688958","credit_to_balance":"0"},"tax_rates_used":[{"totals":{"tax":"434","total":"2500","discount":"0","subtotal":"2066"},"tax_rate":"0.21"}],"adjusted_totals":{"fee":"171","tax":"434","total":"2500","earnings":"1895","subtotal":"2066","grand_total":"2500","currency_code":"EUR"}},"checkout":{"url":"https:\/\/localhost?_ptxn=txn_01j49ym2cj0542kgdpvcp3dkh6"},"payments":[{"amount":"2500","status":"captured","created_at":"2024-08-02T16:27:12.863511Z","error_code":null,"captured_at":"2024-08-02T16:27:15.362606Z","method_details":{"card":{"type":"visa","last4":"4242","expiry_year":2025,"expiry_month":10,"cardholder_name":"Carlos Abrisqueta"},"type":"card"},"payment_method_id":"paymtd_01j49ynbj9g5bzmesmmjez0gv4","payment_attempt_id":"35d34015-64cf-44b6-9f0e-be12448843ef","stored_payment_method_id":"7b58c622-17cb-4ace-8a98-7f0224f7f610"}],"billed_at":"2024-08-02T16:27:15.743194Z","address_id":"add_01j49ymsdc57xvjzarpkdr3epk","created_at":"2024-08-02T16:26:30.762648Z","invoice_id":"inv_01j49ynerdrn6r0qrs0p882z4y","updated_at":"2024-08-02T16:27:16.669507499Z","business_id":null,"custom_data":null,"customer_id":"ctm_01j490w0zypkt01gx4w95mvmm5","discount_id":null,"receipt_data":null,"currency_code":"EUR","billing_period":null,"invoice_number":"8169-10005","billing_details":null,"collection_mode":"automatic","subscription_id":null}}';

    return [$parsedData, $sigHeader, $rawJsonBody];
}
