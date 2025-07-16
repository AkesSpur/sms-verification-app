# Owlet API Integration for Social Media Boosting

This document explains how to set up and use the Owlet API integration for automated social media boosting services.

## Setup

### 1. Environment Configuration

Add the following variables to your `.env` file:

```env
OWLET_API_KEY=your_api_key_here
OWLET_BASE_URL=https://the-owlet.com/api/v2
OWLET_TIMEOUT=30
```

### 2. Database Migration

Run the migrations to add the required database fields:

```bash
php artisan migrate
```

## Features

### 1. Automatic Order Processing

- When users place social media boosting orders, the system automatically attempts to place them via the Owlet API
- If successful, orders are marked as 'processing' and tracked externally
- If the API call fails, orders fall back to 'pending' status for manual processing

### 2. Service Synchronization

- Admin panel includes a "Sync Owlet Services" button
- Fetches available services from Owlet API and creates corresponding products
- Maps external service IDs to local products for order placement

### 3. Order Status Synchronization

- Console command `sync:owlet-orders` periodically checks order statuses
- Updates local order records with external API data
- Tracks progress, remaining quantity, and charges

### 4. External Order Tracking

New database fields added:
- `external_order_id`: Owlet API order ID
- `external_status`: Status from Owlet API
- `external_start_count`: Initial count when order started
- `external_remains`: Remaining quantity to be delivered
- `external_charge`: Actual charge from Owlet API

## Usage

### Admin Panel

1. Navigate to Social Media Products in the admin panel
2. Click "Sync Owlet Services" to import available services
3. Configure product pricing and settings as needed
4. Monitor orders in the Social Media Orders section

### Automated Sync

Set up a cron job to run the sync command regularly:

```bash
# Add to your crontab to run every 5 minutes
*/5 * * * * cd /path/to/your/app && php artisan owlet:sync-orders
```

### Manual Sync

Run the sync command manually:

```bash
php artisan owlet:sync-orders
# or use the alias:
php artisan sync:owlet-orders
```

## API Service Methods

The `OwletApiService` class provides the following methods:

- `getServices()`: Fetch available services
- `placeOrder()`: Place a new order
- `getOrderStatus()`: Check order status
- `getBalance()`: Check account balance
- `refillOrder()`: Request order refill
- `cancelOrder()`: Cancel an order

## Error Handling

- API errors are logged for debugging
- Failed orders fall back to manual processing
- Sync failures are logged but don't affect existing orders
- Toastr notifications provide user feedback in admin panel

## Status Mapping

Owlet API statuses are mapped to internal statuses:

- `Pending` → `pending`
- `In progress` → `processing`
- `Completed` → `completed`
- `Partial` → `processing`
- `Processing` → `processing`
- `Canceled` → `cancelled`
- Default → `pending`

## Security

- API key is stored securely in environment variables
- All API requests include proper authentication
- Input validation prevents malicious data
- Error messages don't expose sensitive information

## Database Seeding

### Owlet Services Seeder

The `OwletServicesSeeder` automatically imports all services from the Owlet API into your database:

```bash
# Run the specific seeder
php artisan db:seed --class=OwletServicesSeeder

# Or run all seeders (includes OwletServicesSeeder)
php artisan db:seed
```

#### What the Seeder Does:

1. **Reads JSON Data**: Processes the `owlet_services_test_2025-07-16_14-34-06.json` file
2. **Creates Categories**: Automatically creates social media categories from service categories
3. **Creates Products**: Maps each service to a social media product with proper categorization
4. **Handles Duplicates**: Updates existing products based on `external_service_id`

#### Seeder Results:
- **209 Categories** created from unique service categories
- **1,322 Products** imported with full service details
- **Automatic Mapping** between products and categories
- **External Service IDs** preserved for API integration

#### Generated Data Structure:
- Categories include emoji-rich names from Owlet API
- Products maintain original pricing, min/max quantities
- Service types (Default, Custom Comments, Subscriptions, etc.) preserved
- Descriptions auto-generated with service features

## Troubleshooting

### Common Issues

1. **API Key Invalid**: Check your Owlet API credentials
2. **No Services Found**: Ensure your Owlet account has active services
3. **Orders Not Syncing**: Check the sync command logs
4. **Connection Timeout**: Adjust the `OWLET_TIMEOUT` setting
5. **Seeder Issues**
   - Ensure the JSON file exists in the project root
   - Check database table structure matches seeder expectations
   - Verify foreign key constraints are properly set

### Logs

Check Laravel logs for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

## Support

For Owlet API specific issues, contact their support team.
For integration issues, check the Laravel logs and ensure all configuration is correct.