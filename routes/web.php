<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view("welcome");
});

Route::get('/app', function () {
    return view('welcome');
});

Auth::routes();

Route::get("/register", [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])
    ->name("register");

Route::post("/register", [\App\Http\Controllers\Auth\RegisterController::class, 'create']);

Route::get("/verify", [\App\Http\Controllers\Auth\VerificationController::class, 'verify'])
    ->name("verify");

Route::get("/resend", [\App\Http\Controllers\Auth\VerificationController::class, 'resend'])
    ->name("resend");

Route::post("/send/reset/link", [\App\Http\Controllers\UserController::class, 'sendResetPasswordLink'])
    ->name("send.reset.link");

Route::get("/reset", [\App\Http\Controllers\UserController::class, 'verifyResetLink'])
    ->name("reset");

Route::get("/reset/password/", [\App\Http\Controllers\UserController::class, 'showPasswordChangeForm'])
    ->name("reset.password.form");

Route::post("/password/update", [\App\Http\Controllers\UserController::class, 'resetPassword'])
    ->name("customer.forgot.password");

Route::group(["middleware" => "auth"], function () {
    Route::get("/email/verify", [\App\Http\Controllers\Auth\VerificationController::class, 'viewVerificationPage'])
        ->name("email.verify");

    Route::group(["prefix" => "location"], function () {
        Route::get("/", [\App\Http\Controllers\LocationController::class, "index"])
            ->name("location");

        Route::post("/", [\App\Http\Controllers\LocationController::class, "store"])
            ->name("location");

        Route::get("/create", [\App\Http\Controllers\LocationController::class, 'create'])
            ->name("location.create");

        Route::get("/default/{id}", [\App\Http\Controllers\LocationController::class, 'makeDefaultLocation'])
            ->name("location.default");

        Route::delete("/{id}", [\App\Http\Controllers\LocationController::class, 'delete'])
            ->name("location.delete");

        Route::get("/address", [\App\Http\Controllers\LocationController::class, 'getAddressFromGoogleApi'])
            ->name("google.address");
    });

    Route::group(["middleware" => ["checkUserStatus", "isAdminCreated", "userHasLocation"]], function () {
        Route::group(["prefix" => "password/change"], function () {
            Route::get("/", [\App\Http\Controllers\UserController::class, 'viewChangePasswordForm'])
                ->name("customer.change.password")
                ->withoutMiddleware(["userHasLocation", "isAdminCreated"]);
            Route::post("/", [\App\Http\Controllers\UserController::class, 'updatePassword'])
                ->name("customer.change.password")
                ->withoutMiddleware(["userHasLocation", "isAdminCreated"]);
        });

        Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

        Route::group(["prefix" => "customer"], function () {
            Route::get("/show/{id}", [\App\Http\Controllers\CustomerController::class, 'show'])
                ->name("user.show");

            Route::put("/update/{id}", [\App\Http\Controllers\CustomerController::class, 'update'])
                ->name("user.update");

            Route::group(["prefix" => "subscription"], function () {
                Route::get("/", [
                    \App\Http\Controllers\Customer\SubscriptionController::class, 'index'
                ])->name("customer.subscription");

                Route::post("/", [
                    \App\Http\Controllers\Customer\SubscriptionController::class, 'store'
                ])->name("customer.subscription");

                Route::get("/{id}", [
                    \App\Http\Controllers\Customer\SubscriptionController::class, 'show'
                ])->name("customer.subscription.edit");

                Route::put("/{id}", [
                    \App\Http\Controllers\Customer\SubscriptionController::class, 'update'
                ])->name("customer.subscription.update");
            });

            Route::group(["prefix" => "charity"], function () {
                Route::get("/", [
                    \App\Http\Controllers\Customer\CharityController::class, 'index'
                ])->name("customer.charity");

                Route::post("/", [
                    \App\Http\Controllers\Customer\CharityController::class, 'store'
                ])->name("customer.charity");

                Route::get("/edit", [
                    \App\Http\Controllers\Customer\CharityController::class, 'show'
                ])->name("customer.charity.edit");

                Route::put("/update", [
                    \App\Http\Controllers\Customer\CharityController::class, 'update'
                ])->name("customer.charity.update");
            });

            Route::group(["prefix" => "order/bin"], function () {
                Route::group(["prefix" => "bin"], function () {
                    Route::get("/", [
                        \App\Http\Controllers\Customer\OrderController::class, 'index'
                    ])->name("customer.order.bin");

                    Route::post("/", [
                        \App\Http\Controllers\Customer\OrderController::class, 'store'
                    ])->name("customer.order.bin");

                    Route::get("confirm", [\App\Http\Controllers\Customer\OrderController::class, 'confirmOrder'])
                        ->name("customer.order.confirm");

                    Route::get("/{id}", [\App\Http\Controllers\Customer\OrderController::class, 'editOrderPage'])
                        ->name("customer.order.edit");

                    Route::put("/{id}", [\App\Http\Controllers\Customer\OrderController::class, 'updateOrderData'])
                        ->name("customer.order.update");
                });

                Route::get("/history", [\App\Http\Controllers\Customer\OrderController::class, 'viewOrderHistoryPage'])
                    ->name("customer.order.history");
            });

            Route::group(["prefix" => "payment"], function () {
                Route::get("/type", [\App\Http\Controllers\Customer\PaymentController::class, 'showPaymentTypePage'])
                    ->name("customer.payment.type");

                Route::post("/type", [\App\Http\Controllers\Customer\PaymentController::class, 'store'])
                    ->name("customer.payment.type");

                Route::get("/{id}", [\App\Http\Controllers\Customer\PaymentController::class, 'show'])
                    ->name("customer.payment.type.show");

                Route::put("/{id}", [\App\Http\Controllers\Customer\PaymentController::class, 'update'])
                    ->name("customer.payment.type.update");
            });

            Route::group(["prefix" => "checkout"], function () {
                Route::get("/", [\App\Http\Controllers\Customer\PaymentController::class, 'viewCheckoutPage'])
                    ->name("customer.checkout");

                Route::post("/", [\App\Http\Controllers\Customer\PaymentController::class, 'storeCheckoutToken'])
                    ->name("customer.checkout");
            });

            Route::group(["prefix" => "account"], function () {
                Route::get("/", [\App\Http\Controllers\Customer\AccountController::class, 'index'])
                    ->name("customer.account");

                Route::post("/", [\App\Http\Controllers\Customer\AccountController::class, 'store'])
                    ->name("customer.account");

                Route::get("/create", [\App\Http\Controllers\Customer\AccountController::class, 'create'])
                    ->name("customer.account.create");

                Route::get("/default/{id}", [
                    \App\Http\Controllers\Customer\AccountController::class, 'makeAccountDefault'
                ])->name("customer.account.default");

                Route::group(["prefix" => "{id}"], function() {
                    Route::get("/", [\App\Http\Controllers\Customer\AccountController::class, 'show'])
                        ->name("customer.account.edit");

                    Route::put("/", [\App\Http\Controllers\Customer\AccountController::class, 'update'])
                        ->name("customer.account.update");

                    Route::delete("/", [\App\Http\Controllers\Customer\AccountController::class, 'delete'])
                        ->name("customer.account.delete");
                });
            });

            Route::group(["prefix" => "pickup"], function () {
                Route::get("/", [\App\Http\Controllers\Customer\PickupController::class, 'index'])
                    ->name("customer.pickup");

                Route::post("/", [\App\Http\Controllers\Customer\PickupController::class, 'store'])
                    ->name("customer.pickup");

                Route::get("/view", [\App\Http\Controllers\Customer\PickupController::class, 'showPickups'])
                    ->name("customer.pickup.view");

//                Route::get("/show", [\App\Http\Controllers\Customer\PickupController::class, 'showPickupForm'])
//                    ->name("customer.pickup.show");

                Route::post("/check", [\App\Http\Controllers\Customer\PickupController::class, 'checkPickup'])
                    ->name("customer.pickup.check");

                Route::get("/date/{id}", [\App\Http\Controllers\Customer\PickupController::class, 'pickupDate'])
                    ->name("customer.pickup.date.show");

                Route::put("/date/{id}", [\App\Http\Controllers\Customer\PickupController::class, 'updatePickupDate'])
                    ->name("customer.pickup.date.update");
            });

            Route::group(["prefix" => "notification"], function () {
                Route::get("/", [\App\Http\Controllers\Customer\NotificationController::class, 'index'])
                    ->name("customer.notification");

                Route::post("/", [\App\Http\Controllers\Customer\NotificationController::class, 'store'])
                    ->name("customer.notification");

                Route::get("/all", [\App\Http\Controllers\Customer\NotificationController::class, 'listAllNotifications'])
                    ->name("customer.notifications");

                Route::group(["prefix" => "admin"], function () {
                    Route::get("/", [\App\Http\Controllers\Customer\AdminNotificationController::class, 'index'])
                        ->name("customer.admin.notification");

                    Route::delete("/{id}", [
                        \App\Http\Controllers\Customer\AdminNotificationController::class, 'deleteNotification'
                    ])->name("customer.admin.notification.delete");
                });
            });

            Route::get("/refunds", [\App\Http\Controllers\MyobTransactionController::class, 'index'])
                ->name("customer.refunds");
        });
    });

    Route::group(["middleware" => "isNotCustomer"], function () {
        Route::get("/bin/info/{qr_code}", [\App\Http\Controllers\BinController::class, 'binInfo'])
            ->name("bin.info");

        Route::get("/user/address/{user_id}", [\App\Http\Controllers\LocationController::class, 'getUserAddresses'])
            ->name("user.addresses");

        Route::group(["prefix" => "driver/pickup"], function () {
            Route::get("/", [\App\Http\Controllers\DriverPickupController::class, 'index'])
                ->name("driver.pickup");

            Route::get("/{bin_id}", [\App\Http\Controllers\DriverPickupController::class, 'store'])
                ->name("driver.pickup.bin");

            Route::delete("/{id}", [\App\Http\Controllers\DriverPickupController::class, 'delete'])
                ->name("driver.pickup.delete");
        });

        Route::get("/user/orders/{user_id}", [\App\Http\Controllers\OrderController::class, 'getOrdersByUserId'])
            ->name("user.orders");

        Route::post("/user/bin/assign/", [\App\Http\Controllers\BinController::class, 'assignBinToUser'])
            ->name("user.bin.assign");
    });

    Route::group(["middleware" => "isUserAdmin"], function () {
        Route::group(["prefix" => "role"], function () {
            Route::get("/", [\App\Http\Controllers\RoleController::class, 'index'])
                ->name('role');

            Route::post("/", [\App\Http\Controllers\RoleController::class, 'store'])
                ->name("role");

            Route::get("/{id}", [\App\Http\Controllers\RoleController::class, 'show'])
                ->name("role.edit");

            Route::put("/{id}", [\App\Http\Controllers\RoleController::class, 'update'])
                ->name("role.update");

            Route::delete("/{id}", [\App\Http\Controllers\RoleController::class, 'delete'])
                ->name("role.delete");

        });

        Route::group(['prefix' => "customer"], function () {
            Route::get("/", [\App\Http\Controllers\CustomerController::class, 'index'])
                ->name("customer");

            Route::post("/", [\App\Http\Controllers\CustomerController::class, 'store'])
                ->name("customer");

            Route::get("/create", [\App\Http\Controllers\CustomerController::class, 'create'])
                ->name("customer.create");

            Route::get("/off-board/{id}", [\App\Http\Controllers\CustomerController::class, 'offBoard'])
                ->name("customer.off-board");

            Route::group(["prefix" => "{id}"], function () {
                Route::get("/", [\App\Http\Controllers\CustomerController::class, 'show'])
                    ->name("customer.show");

                Route::put("/", [\App\Http\Controllers\CustomerController::class, 'update'])
                    ->name("customer.update");

                Route::delete("/", [\App\Http\Controllers\CustomerController::class, 'destroy'])
                    ->name("customer.delete");
            });
        });

        Route::group(["prefix" => "subscription"], function () {
            Route::get("/", [\App\Http\Controllers\SubscriptionController::class, 'index'])
                ->name("subscription");

            Route::get("/create", [\App\Http\Controllers\SubscriptionController::class, 'create'])
                ->name("subscription.create");

            Route::post("/", [\App\Http\Controllers\SubscriptionController::class, 'store'])
                ->name("subscription");

            Route::get("/{id}", [\App\Http\Controllers\SubscriptionController::class, 'show'])
                ->name("subscription.edit");

            Route::put("/{id}", [\App\Http\Controllers\SubscriptionController::class, 'update'])
                ->name("subscription.update");

            Route::delete("/{id}", [\App\Http\Controllers\SubscriptionController::class, 'delete'])
                ->name("subscription.delete");
        });

        Route::group(["prefix" => "charity"], function () {
            Route::get("/", [\App\Http\Controllers\CharityController::class, 'index'])
                ->name("charity");

            Route::get("/create", [\App\Http\Controllers\CharityController::class, 'create'])
                ->name('charity.create');

            Route::post("/", [\App\Http\Controllers\CharityController::class, 'store'])
                ->name("charity");

            Route::group(["prefix" => '{id}'], function () {
                Route::get("/", [\App\Http\Controllers\CharityController::class, 'show'])
                    ->name("charity.edit");

                Route::put("/", [\App\Http\Controllers\CharityController::class, 'update'])
                    ->name("charity.update");

                Route::delete("/", [\App\Http\Controllers\CharityController::class, 'delete'])
                    ->name("charity.delete");
            });
        });

        Route::group(["prefix" => "announcement"], function () {
            Route::get("/", [\App\Http\Controllers\AnnouncementController::class, 'index'])
                ->name("announcement");

            Route::get("/create", [\App\Http\Controllers\AnnouncementController::class, 'create'])
                ->name("announcement.create");

            Route::post("/", [\App\Http\Controllers\AnnouncementController::class, 'store'])
                ->name("announcement");

            Route::group(["prefix" => "{id}"], function () {
                Route::get("/", [\App\Http\Controllers\AnnouncementController::class, 'show'])
                    ->name("announcement.edit");

                Route::put("/", [\App\Http\Controllers\AnnouncementController::class, 'update'])
                    ->name("announcement.update");

                Route::delete("/", [\App\Http\Controllers\AnnouncementController::class, 'delete'])
                    ->name("announcement.delete");
            });
        });

        Route::group(["prefix" => "orders"], function () {
            Route::get("/", [\App\Http\Controllers\OrderController::class, 'index'])
                ->name("orders");

            Route::post("/", [\App\Http\Controllers\OrderController::class, 'store'])
                ->name("order");

            Route::get("/create", [\App\Http\Controllers\OrderController::class, 'create'])
                ->name("order.create");

            Route::group(["prefix" => "pickup"], function () {
                Route::get("/", [\App\Http\Controllers\PickupController::class, 'index'])
                    ->name("pickup");

                Route::post("/", [\App\Http\Controllers\PickupController::class, 'store'])
                    ->name("pickup");

                Route::get("/create", [\App\Http\Controllers\PickupController::class, 'create'])
                    ->name("pickup.create");

                Route::group(["prefix" => "{id}"], function () {
                    Route::get("/", [\App\Http\Controllers\PickupController::class, 'edit'])
                        ->name("pickup.edit");

                    Route::put("/", [\App\Http\Controllers\PickupController::class, 'updatePickup'])
                        ->name("pickup.update");

                    Route::delete("/", [\App\Http\Controllers\PickupController::class, 'delete'])
                        ->name("pickup.delete");
                });

                Route::get("/assign/{id}", [\App\Http\Controllers\PickupController::class, 'showPickUpForm'])
                    ->name("pickup.assign");

                Route::put("/assign/{id}", [\App\Http\Controllers\PickupController::class, 'update'])
                    ->name("pickup.assign");

                Route::get("/accept/{id}", [\App\Http\Controllers\PickupController::class, "acceptStatus"])
                    ->name("pickup.accept");
            });

            Route::group(["prefix" => "{id}"], function () {
                Route::get("/", [\App\Http\Controllers\OrderController::class, "edit"])
                    ->name("order.edit");

                Route::put("/", [\App\Http\Controllers\OrderController::class, "update"])
                    ->name("order.update");

                Route::delete("/", [\App\Http\Controllers\OrderController::class, 'delete'])
                    ->name("order.delete");
            });
        });

        Route::group(["prefix" => "bin"], function () {
            Route::get("/", [\App\Http\Controllers\BinController::class, 'index'])
                ->name("bin");

            Route::post("/", [\App\Http\Controllers\BinController::class, "store"])
                ->name("bin");

            Route::get("/create", [\App\Http\Controllers\BinController::class, "create"])
                ->name("bin.create");

            Route::get("/print", [\App\Http\Controllers\BinController::class, 'printView'])
                ->name("bin.print");

            Route::get("/decompose/{id}", [\App\Http\Controllers\BinController::class, 'decompose'])
                ->name("bin.decompose");

            Route::group(["prefix" => "assign"], function () {
                Route::get("/{id}", [\App\Http\Controllers\BinController::class, 'showAssignBinForm'])
                    ->name("bin.assign.show");

                Route::post("/", [\App\Http\Controllers\BinController::class, 'assignBinToOrder'])
                    ->name("bin.assign");
            });

            Route::group(["prefix" => "{id}"], function () {
                Route::get("/", [\App\Http\Controllers\BinController::class, 'show'])
                    ->name("bin.edit");

                Route::put("/", [\App\Http\Controllers\BinController::class, 'update'])
                    ->name("bin.update");

                Route::delete("/", [\App\Http\Controllers\BinController::class, 'delete'])
                    ->name("bin.delete");
            });
        });

        Route::group(["prefix" => "account"], function () {
            Route::get("/", [\App\Http\Controllers\Customer\AccountController::class, 'index'])
                ->name("account");

            Route::post("/", [\App\Http\Controllers\Customer\AccountController::class, 'store'])
                ->name("account");

            Route::get("/create", [\App\Http\Controllers\Customer\AccountController::class, 'create'])
                ->name("account.create");

            Route::get("/default/{id}", [
                \App\Http\Controllers\Customer\AccountController::class, 'makeAccountDefault'
            ])->name("account.default");

            Route::group(["prefix" => "{id}"], function() {
                Route::get("/", [\App\Http\Controllers\Customer\AccountController::class, 'show'])
                    ->name("account.edit");

                Route::put("/", [\App\Http\Controllers\Customer\AccountController::class, 'update'])
                    ->name("account.update");

                Route::delete("/", [\App\Http\Controllers\Customer\AccountController::class, 'delete'])
                    ->name("account.delete");
            });
        });

        Route::get("/notifications", [ \App\Http\Controllers\NotificationController::class, 'index'])
            ->name("notification");

        Route::delete("/notification/{id}", [\App\Http\Controllers\NotificationController::class, 'delete'])
            ->name("notification.delete");

        Route::get("/addresses/{id}", [\App\Http\Controllers\UserController::class, 'getAddresses'])
            ->name("customer.address");

        Route::group(["prefix" => "driver"], function () {
            Route::get("/", [\App\Http\Controllers\DriverController::class, 'index'])
                ->name("driver");

            Route::post("/", [\App\Http\Controllers\DriverController::class, 'store'])
                ->name("driver");

            Route::get("/create", [\App\Http\Controllers\DriverController::class, 'create'])
                ->name("driver.create");

            Route::group(["prefix" => "{id}"], function () {
                Route::get("/", [\App\Http\Controllers\DriverController::class, 'edit'])
                    ->name("driver.edit");

                Route::put("/", [\App\Http\Controllers\DriverController::class, 'update'])
                    ->name("driver.update");

                Route::delete("/", [\App\Http\Controllers\DriverController::class, 'delete'])
                    ->name("driver.delete");
            });
        });

        Route::group(["prefix" => "stats"], function () {
            Route::get("/orders", [\App\Http\Controllers\HomeController::class, 'chartStats'])
                ->name("stats.orders");
        });

        Route::group(["prefix" => "admin/notification"], function () {
            Route::get("/", [\App\Http\Controllers\AdminNotificationController::class, 'index'])
                ->name("admin.notification");

            Route::post("/", [\App\Http\Controllers\AdminNotificationController::class, 'store'])
                ->name("admin.notification");

            Route::get("/create", [\App\Http\Controllers\AdminNotificationController::class, 'create'])
                ->name("admin.notification.create");

            Route::delete("/{id}", [\App\Http\Controllers\AdminNotificationController::class, 'delete'])
                ->name("admin.notification.delete");
        });

        Route::get("/tools", [\App\Http\Controllers\ToolController::class, 'showToolsPage'])
            ->name("tools");

        Route::group(["prefix" => "import"], function () {
            Route::post("/customer", [\App\Http\Controllers\ToolController::class, 'importCustomers'])
                ->name("import.customer");

            Route::post("/bin", [\App\Http\Controllers\ToolController::class, 'importBins'])
                ->name("import.bin");
        });

        Route::get("/myob/supplier", [\App\Http\Controllers\MyobController::class, 'syncCustomer'])
            ->name("myob.supplier");

        Route::get("/myob/supplier/refunds", [\App\Http\Controllers\MyobController::class, 'syncCustomerRefunds'])
            ->name("myob.supplier.refunds");
    });
});
