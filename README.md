## Fridge master

### Setup

Standard laravel app installation

Used sqlite to save time on database configuration. So, it is needed to be created.
``` bash
touch database/database.sqlite 
```

### Some information

Only one endpoint is working here. And it is without authentication. Just for testing.

At first, made a normalized version of storing booked blocks. 
Stored booked block ids in the separate table with booking id. 
It worked fine, but when booking become more, it affected the performance.

Then, I denormalized the storage of booked blocks. Stored block ids in bookings table. With this approach, 
performance is good even with thousands of bookings.

I kept both versions, just commented out the normalized version. 
You can look and test it out for yourselves.

There is a seeder for locations and blocks. 
``` bash
php artisan db:seed 
```

And there are seeders for bookings (for both versions). You can seed them for testing.
