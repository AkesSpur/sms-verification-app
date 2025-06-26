# Database Structure Improvement

This document outlines the comprehensive improvements made to the SMS verification application's database structure to address the issues with orders, services, and country pricing.

## Overview of Changes

The database structure has been completely redesigned to:

1. **Improve Order Management**: Better status tracking, 20-minute SMS window handling, and proper cancellation logic
2. **Enhance Service Availability**: Real-time availability tracking and proper service status management
3. **Better Pricing Structure**: Separate API pricing from final pricing with markup tracking
4. **Audit Trail**: Complete status change history for orders
5. **Performance Optimization**: Better indexing and query optimization

## New Database Structure

### 1. Orders Table Improvements

**New Migration**: `2025_03_17_000001_improve_orders_table_structure.php`

**Key Changes**:
- `country` (string) → `country_id` (foreign key)
- Enhanced `status` enum with specific states
- Added `sms_window_expires_at` for 20-minute SMS window tracking
- Added pricing breakdown (`api_price`, `markup_percentage`, `final_price`)
- Added cancellation tracking (`can_cancel`, `cancelled_at`, `cancellation_reason`)
- Added retry mechanism (`last_retry_at`, `max_retries`)
- Added order source tracking and API response storage

**New Status Values**:
- `pending`: Order created, waiting for number assignment
- `active`: Number assigned, waiting for SMS
- `completed`: SMS received successfully
- `expired`: Order expired due to timeout
- `cancelled`: Order cancelled by user or system
- `failed`: Order failed due to technical issues
- `refunded`: Order refunded

### 2. Services Table Improvements

**New Migration**: `2025_03_17_000002_improve_services_table_structure.php`

**Key Changes**:
- Enhanced `status` enum for better service state management
- Added availability tracking (`is_available`, `available_numbers`)
- Added service configuration (`max_retry_attempts`, `sms_timeout_minutes`)
- Added API configuration (`api_service_code`, `api_config`)
- Added pricing configuration (`base_price`, `markup_percentage`)
- Added service statistics (`total_orders`, `successful_orders`, `success_rate`)
- Added metadata (`description`, `icon`, `sort_order`)

### 3. Country-Service Pivot Table Improvements

**New Migration**: `2025_03_17_000003_improve_country_service_table_structure.php`

**Key Changes**:
- Added per-country availability tracking
- Added detailed pricing breakdown
- Added per-country statistics
- Added rate limiting configuration
- Added status tracking per country-service combination
- Added API response tracking

### 4. Order Status History

**New Migration**: `2025_03_17_000004_create_order_statuses_table.php`

**Purpose**: Complete audit trail of all order status changes

**Features**:
- Tracks who changed the status (system, user, admin, API)
- Stores reason for status change
- Includes metadata for additional context
- Timestamped changes for audit purposes

## Model Improvements

### Order Model Enhancements

**New Methods**:
- `isSmsWindowExpired()`: Check if 20-minute window has passed
- `canBeCancelled()`: Determine if order can be cancelled
- `shouldBeAutoCancelled()`: Check if order should be auto-cancelled
- `setSmsWindowExpiration()`: Set the SMS window expiration time
- `markSmsReceived()`: Mark SMS as received and complete order
- `cancel()`: Cancel order with proper logging
- `logStatusChange()`: Log status changes to history table

**New Scopes**:
- `needsAutoCancellation()`: Find orders that need auto-cancellation
- `active()`: Get active orders

### Service Model Enhancements

**New Methods**:
- `isAvailable()`: Check if service is available
- `isAvailableInCountry()`: Check availability in specific country
- `updateAvailabilityForCountry()`: Update availability data
- `updateStatsForCountry()`: Update success/failure statistics
- `getSmsTimeoutMinutes()`: Get SMS timeout for service
- `getMaxRetryAttempts()`: Get max retry attempts

**New Scopes**:
- `available()`: Get available services
- `active()`: Get active services

## Console Commands

### 1. Process Expired Orders

**Command**: `php artisan orders:process-expired`

**Purpose**: Automatically cancel orders that have exceeded the 20-minute SMS window

**Features**:
- Finds orders where `sms_window_expires_at` has passed
- Only processes orders without received SMS
- Logs all cancellations for audit
- Supports dry-run mode for testing

**Usage**:
```bash
# Process expired orders
php artisan orders:process-expired

# Dry run to see what would be processed
php artisan orders:process-expired --dry-run
```

### 2. Update Service Availability

**Command**: `php artisan services:update-availability`

**Purpose**: Update service availability and pricing from SMS API

**Features**:
- Fetches real-time availability from SMS API
- Updates pricing with markup calculation
- Can target specific services or countries
- Includes rate limiting to avoid API abuse

**Usage**:
```bash
# Update all services
php artisan services:update-availability

# Update specific service
php artisan services:update-availability --service=wa

# Update specific country
php artisan services:update-availability --country=0

# Dry run
php artisan services:update-availability --dry-run
```

## Implementation Steps

### 1. Run Migrations

```bash
# Run the new migrations
php artisan migrate
```

### 2. Seed Improved Data

```bash
# Run the improved database seeder
php artisan db:seed --class=ImprovedDatabaseSeeder
```

### 3. Schedule Commands

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Process expired orders every 5 minutes
    $schedule->command('orders:process-expired')
             ->everyFiveMinutes()
             ->withoutOverlapping();
    
    // Update service availability every 15 minutes
    $schedule->command('services:update-availability')
             ->everyFifteenMinutes()
             ->withoutOverlapping();
}
```

### 4. Update Controllers

Update your controllers to use the new model methods:

```php
// In UsaNumberController.php
public function cancel(Order $order)
{
    if (!$order->canBeCancelled()) {
        return response()->json(['error' => 'Order cannot be cancelled'], 400);
    }
    
    $order->cancel('Cancelled by user', 'user', auth()->id());
    
    return response()->json(['message' => 'Order cancelled successfully']);
}

public function checkStatus(Order $order)
{
    // Check if order should be auto-cancelled
    if ($order->shouldBeAutoCancelled()) {
        $order->cancel('Auto-cancelled: SMS window expired');
    }
    
    return response()->json([
        'status' => $order->status,
        'can_cancel' => $order->canBeCancelled(),
        'sms_window_expired' => $order->isSmsWindowExpired(),
        'sms_code' => $order->sms_code
    ]);
}
```

## Key Benefits

### 1. 20-Minute SMS Window Handling
- Automatic tracking of SMS reception window
- Auto-cancellation of expired orders
- Proper cancellation logic based on SMS API behavior

### 2. Improved Service Availability
- Real-time availability tracking
- Per-country availability status
- Automatic service status updates

### 3. Better Pricing Structure
- Separation of API price and final price
- Configurable markup percentages
- Price history tracking

### 4. Enhanced Order Management
- Complete status history
- Proper cancellation workflow
- Retry mechanism with limits
- Audit trail for all changes

### 5. Performance Improvements
- Better database indexing
- Optimized queries with scopes
- Reduced redundant API calls

## Migration Notes

### Data Migration
The `ImprovedDatabaseSeeder` handles:
- Mapping old country strings to country IDs
- Converting old status values to new enum values
- Creating initial status history records
- Setting up proper pricing structure

### Backward Compatibility
The migrations are designed to be backward compatible:
- Old columns are not immediately dropped
- New columns have sensible defaults
- Gradual migration approach

## Monitoring and Maintenance

### 1. Monitor Order Cancellations
```bash
# Check recent auto-cancellations
php artisan tinker
>>> App\Models\Order::where('status', 'cancelled')
    ->where('cancellation_reason', 'like', '%Auto-cancelled%')
    ->whereDate('cancelled_at', today())
    ->count()
```

### 2. Monitor Service Availability
```bash
# Check service availability status
php artisan tinker
>>> App\Models\Service::with('countries')
    ->get()
    ->map(fn($s) => [
        'service' => $s->name,
        'available' => $s->isAvailable(),
        'countries' => $s->activeCountries()->count()
    ])
```

### 3. Performance Monitoring
- Monitor the scheduled commands execution time
- Track API call frequency and response times
- Monitor database query performance

## Troubleshooting

### Common Issues

1. **Orders not auto-cancelling**
   - Check if the scheduled command is running
   - Verify `sms_window_expires_at` is set correctly
   - Check command logs for errors

2. **Service availability not updating**
   - Verify API credentials are correct
   - Check for rate limiting issues
   - Review command execution logs

3. **Performance issues**
   - Ensure database indexes are created
   - Monitor slow query log
   - Consider adding more specific indexes

This improved database structure provides a solid foundation for reliable SMS verification service management with proper order lifecycle handling and real-time service availability tracking.