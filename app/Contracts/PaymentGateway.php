<?php
// app/Contracts/PaymentGateway.php

namespace App\Contracts;

interface PaymentGateway
{
    // Basic payment operations only
    public function createCustomer(array $data): array;
    public function createPayment(array $data): array;
    public function createSubscription(array $data): array;
    public function refundPayment(string $paymentId, ?int $amount = null): array;
    
    // Basic subscription operations  
    public function cancelSubscription(string $subscriptionId): array;
}