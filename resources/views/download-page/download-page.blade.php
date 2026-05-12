<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title>download-page</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="64x64" href="assets/img/favicon.png" />
    <link rel="icon" type="image/png" sizes="64x64" href="assets/img/favicon.png" />

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7cRaleway:300,300i,400,400i,500,500i,600,600i,700,700i%7cPoppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />
    <!-- Vendor CSS Files -->
    <link href="https://mahakal.in/assets/vendor/animate.css/animate.min.css" rel="stylesheet" />
    <link href="https://mahakal.in/assets/vendor/aos/aos.css" rel="stylesheet" />
    <link href="https://mahakal.in/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://mahakal.in/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://mahakal.in/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://mahakal.in/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet" />
    <link href="https://mahakal.in/assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="https://mahakal.in/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />

    <!-- Template Main CSS File -->
    <link href="https://mahakal.in/assets/css/style.css" rel="stylesheet" />
    <style>
        #hero {
            height: 570px;
        }

        @media (max-width: 375px) {
            #hero {
                height: 562px !important;
            }

            #topbar {
                font-size: 13px;
            }
        }

        @media (max-width: 991px) {
            #hero {
                height: 555px;
            }

            #hero .hero-img {
                margin-top: 100px !important;
                text-align: center;
            }
        }

        @media (max-width: 767px) {
            .text-xs-center {
                text-align: center !important;
                margin-top: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- ======= Top Bar ======= -->
    <div id="topbar" class="fixed-top d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="fa fa-envelope" aria-hidden="true"></i><a
                    href="mailto:contact@example.com">contact@mahakal.com</a>
                <i class="fa fa-phone-square phone-icon"></i>
                <!--<a href="tel:+919424582045">+91 9424582045</a>,-->Toll free :
                &nbsp;<a href="tel:08069645013">08069645013</a>
            </div>
            <div class="cta d-none d-md-block">
                <a href="#about" class="scrollto">Get Started</a>
            </div>
        </div>
    </div>

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="d-flex flex-column justify-content-end align-items-center">
        <div id="particles-js">
            <canvas class="particles-js-canvas-el" width="1343" height="630"
                style="width: 100%; height: 100%"></canvas>
        </div>

        <div class="container zi">
            <div class="row">
                <div class="col-lg-7 pt-lg-0 order-2 order-lg-1 text-xs-center">
                    <h2>Revolutionizing Sanatan</h2>
                    <h1>The World's Largest Devotional Platform</h1>
                    <a href="javascript:void(0);" class="wrapper" onclick="openStoreLink()">
                        <img src="{{ asset('public/assets/front-end/img/icons/google-play.png') }}" alt=""
                            class="img-fluid" style="width: 220px;">
                    </a>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 hero-img">
                    <img src="https://mahakal.in/assets/img/banner.gif" width="420" height="420"
                        class="img-fluid spin" alt="" />
                </div>
            </div>
        </div>

        {{-- <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 24 150 28 " preserveAspectRatio="none">
            <defs>
                <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
                </path>
            </defs>
            <g class="wave1">
                <use xlink:href="#wave-path" x="50" y="3" fill="rgba(255,255,255, .1)"></use>
            </g>
            <g class="wave2">
                <use xlink:href="#wave-path" x="50" y="0" fill="rgba(255,255,255, .2)"></use>
            </g>
            <g class="wave3">
                <use xlink:href="#wave-path" x="50" y="9" fill="#fff"></use>
            </g>
        </svg> --}}
    </section>
    <!-- End Hero -->

    <main id="main">
        <!-- ======= Contact Section ======= -->

        <!-- End Contact Section -->
    </main>
    <!-- End #main -->
    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Mahakal.com</span></strong>. All Rights Reserved
            </div>

            <div class="credits">
                Powered and Run by
                <a href="#">Mahakal AstroTech Pvt. Ltd.</a>&nbsp;&nbsp; Designed and
                Developed by <a href="#">Manal Softech Pvt. Ltd.</a>
            </div>
        </div>
    </footer>
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="fa fa-arrow-up"></i></a>

    <!-- Vendor JS Files -->
    <script src="https://mahakal.in/assets/vendor/aos/aos.js"></script>
    <script src="https://mahakal.in/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://mahakal.in/assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="https://mahakal.in/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="https://mahakal.in/assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="https://mahakal.in/assets/js/main.js"></script>
    <script src="https://mahakal.in/assets/js/particles.js"></script>

    <script>
        particlesJS("particles-js", {
            particles: {
                number: {
                    value: 100,
                    density: {
                        enable: true,
                        value_area: 800,
                    },
                },
                color: {
                    value: "#ffffff",
                },
                shape: {
                    type: "circle",
                    stroke: {
                        width: 0,
                        color: "#000000",
                    },
                    polygon: {
                        nb_sides: 5,
                    },
                    image: {
                        src: "img/github.svg",
                        width: 100,
                        height: 100,
                    },
                },
                opacity: {
                    value: 0.6,
                    random: false,
                    anim: {
                        enable: false,
                        speed: 1,
                        opacity_min: 0.1,
                        sync: false,
                    },
                },
                size: {
                    value: 3,
                    random: true,
                    anim: {
                        enable: false,
                        speed: 40,
                        size_min: 0.1,
                        sync: false,
                    },
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#ffffff",
                    opacity: 0.4,
                    width: 1,
                },
                move: {
                    enable: true,
                    speed: 6,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    bounce: false,
                    attract: {
                        enable: false,
                        rotateX: 600,
                        rotateY: 1200,
                    },
                },
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: {
                        enable: true,
                        mode: "grab",
                    },
                    onclick: {
                        enable: true,
                        mode: "push",
                    },
                    resize: true,
                },
                modes: {
                    grab: {
                        distance: 140,
                        line_linked: {
                            opacity: 1,
                        },
                    },
                    bubble: {
                        distance: 400,
                        size: 40,
                        duration: 2,
                        opacity: 8,
                        speed: 3,
                    },
                    repulse: {
                        distance: 200,
                        duration: 0.4,
                    },
                    push: {
                        particles_nb: 4,
                    },
                    remove: {
                        particles_nb: 2,
                    },
                },
            },
            retina_detect: true,
        });
    </script>
    <script>
        function openStoreLink() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;

            // Define Play Store and App Store Links
            var playStoreUrl = "https://play.google.com/store/apps/details?id=manal.mahakal.com";
            var appStoreUrl = "https://apps.apple.com/in/app/mahakal-com/id6475806433";

            // Detect iOS
            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                window.location.href = "https://apps.apple.com/in/app/mahakal-com/id6475806433"; // Open App Store
                setTimeout(() => {
                    window.location.href = appStoreUrl; // Fallback to web
                }, 2000);
            }
            // Detect Android
            else if (/android/i.test(userAgent)) {
                window.location.href = "https://play.google.com/store/apps/details?id=manal.mahakal.com"; // Open Play Store
                setTimeout(() => {
                    window.location.href = playStoreUrl; // Fallback to web
                }, 2000);
            }
            // Default Web Store Link
            else {
                window.location.href = playStoreUrl; // Default to Play Store Web Link
            }
        }
    </script>
</body>

</html>
