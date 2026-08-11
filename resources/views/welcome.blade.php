<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>réhat</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .navbar {        
            backdrop-filter: blur(px);
            transition: 0.3s ease;
            padding: 15px 0;
        }

        .navbar-scrolled {
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }

        .navbar-brand img {
            height: 35px;
        }

        .nav-link {
            font-weight: 500;
            color: #f5360bff !important;
            margin-left: 15px;
        }

        .hero-section {
            min-height: 100vh;
            background:
                linear-gradient(rgba(0, 0, 0, 0.55),
                    rgba(0, 0, 0, 0.55)),
                url('{{ asset('images/bgpkm.jpeg') }}');
            background-size: cover;
            background-position: center;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;

            padding: 120px 20px;
        }

        .hero-section h1 {
            font-size: 60px;
            font-weight: 800;
            margin-top: 20px;
        }

        .hero-section p {
            font-size: 22px;
            max-width: 700px;
            margin: auto;
        }

        .btn-custom {
            background: #f5360bff;
            border: none;
            color: white;
            padding: 14px 35px;
            border-radius: 12px;
            font-weight: 600;
            margin-top: 30px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background: #f5360bff;
            color: white;
        }

        .section-title {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 35px 25px;
            height: 100%;
            transition: 0.3s ease;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .feature-icon {
            color: #f5360bff;
        }

        .about-image-wrapper img {
            border-radius: 20px;
            width: 100%;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        footer {
            background: #111827;
        }

        footer a {
            text-decoration: none;
        }

        @media(max-width: 768px) {

            .hero-section h1 {
                font-size: 38px;
            }

            .hero-section p {
                font-size: 18px;
            }

            .section-title {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>
    <!-- Hero -->
    <main>

        <section id="home" class="hero-section">

            <div class="container">                  

                <h1>
                    Selamat Datang di réhat
                </h1>

                <p class="mt-4">
                    Sistem Informasi Raport Kesehatan 
                </p>

                <a href="/admin"
                    class="btn btn-custom">
                    Masuk Sistem
                </a>

            </div>

        </section>

    </main>

    <!-- Footer -->
    <footer class="text-white py-4">

        <div class="container text-center">

            <p class="mb-2">
                ©réhat dev team.                
            </p>        

        </div>

    </footer>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener('scroll', () => {

            const navbar =
                document.querySelector('.navbar');

            if (window.scrollY > 50) {

                navbar.classList.add(
                    'navbar-scrolled'
                );

            } else {

                navbar.classList.remove(
                    'navbar-scrolled'
                );
            }
        });
    </script>

</body>

</html>