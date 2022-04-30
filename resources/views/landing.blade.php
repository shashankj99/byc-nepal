
<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Backyard Cash</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover"/>
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset("css/app.css") }}">
    <link rel="stylesheet" href="{{ asset("css/style.css") }}">

    @laravelPWA

    <script>
        document.onreadystatechange = function(e)
        {
            if (document.readyState === 'complete')
            {
                const isAppInstalled = localStorage.getItem("installed");

                if (isAppInstalled === "true")
                    window.location.replace("{{ url("/app") }}");
            }
        };
        // window.addEventListener('load', (e) => {
        //     e.preventDefault();
        //
        //     // console.log(isAppInstalled);
        // });
    </script>
</head>

<body>

<header class="main-header ">
    <img src="{{ asset("images/landing/header.jpg") }}" alt="" class="w-100">
</header>

<main>
    <section class="main-body">
        <div class="container">

            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center">South Australia's leading HOME and COMMERCIAL bottle and can
                        pick-up service. WE COME TO YOU!</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="content-button">
                        <a href="#" id="install-app"><img src="{{ asset("images/landing/button.png") }}" alt=""></a>
                    </div>

                    <p>No more messy bottles and cans laying around or loading up the car to go to the recycling depot -
                        <strong>We come to you!</strong></p>

                    <p>Choose between:</p>
                    <ul class="list-unstyled">
                        <li>- a FREE 200 litre drum or</li>
                        <li>- a 240 litre wheelie bin (*with a $40 hire fee)</li>
                    </ul>

                    <p><strong>We pay 7c for every refundable item collected.</strong></p>

                    <ul class="list-unstyled mt-4 mb-4">
                        <li>- All staff have police clearance</li>
                        <li>- A clean bin is provided on pick-up</li>
                        <li>- Unique number on each bin for payment security</li>
                        <li>- Prompt and reliable home pick-up</li>
                        <li>- Polite, qualified staff</li>
                        <li>- Prompt and accurate payment</li>
                        <li>- Donate your refunds to one of our Charity parrtners</li>
                    </ul>

                    <p class="small">*The $40 fee can be paid upfront or we can deduct $10 from your first 4 pick-ups.</p>

                    <div class="container-block mt-4">
                        <img src="{{ asset("images/landing/container.png") }}" alt="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="area">
                        <h2 class="text-center">Area of operation</h2>

                        <p>We pick-up as far north as Dublin, to Coromandel Valley down south and Williamstown to the east.</p>

                        <p> If your area isn't listed, please call: <a href="tel:">0420 355 085</a> or send us an email - new
                            areas are added regularly.</p>

                        <div class="map-container mt-4">
                            <img src="{{ asset("images/landing/map.png") }}" alt="" class="w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="customers">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center">
                        Commercial customers who recycle with Backyard Cash
                    </h2>

                    <div class="customers-block">
                        <div class="row align-items-center">
                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/coles.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/woolworths.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/adelaide-ufc.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/kfc.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/inghams.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/kennards.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/big-rocking-horse.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/comfort.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/atura.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/nandos.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/hsa.png") }}" alt=""></a>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <div class="single-customer text-center">
                                    <a href="#"><img src="{{ asset("images/landing/hoyts.png") }}" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<footer class="main-footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <a href="#" id="install-app"><img src="{{ asset("images/landing/button1.png") }}" alt=""></a>
            </div>

            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li><a href="https://www.facebook.com/Backyard-Cash-235118287237135" target="_blank"><img src="{{ asset("images/landing/facebook.png") }}" alt=""></a></li>
                    <li><a href="https://www.instagram.com/backyard.cash/" target="_blank"><img src="{{ asset("images/landing/instagram.png") }}" alt=""></a></li>
                    <li><a href="tel:+610420355085"><img src="{{ asset("images/landing/phone.png") }}" alt=""></a></li>
                    <li><a href="mailto: orders@backyardcash.net.au"><img src="{{ asset("images/landing/mail.png") }}" alt=""></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script type="text/javascript">
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
    });

    const installApp = document.getElementById('install-app');

    installApp.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                deferredPrompt = null;
                localStorage.setItem("installed", "true");
            }
        }
    });
</script>

</body>

</html>

