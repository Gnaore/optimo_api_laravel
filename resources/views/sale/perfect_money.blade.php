<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/heroes/">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

    <!-- Custom styles for this template -->
    <link href="{{asset('css/heroes.css')}}" rel="stylesheet">
</head>

<body style="margin: 0; padding: 0; width: 100%; word-break: break-word; -webkit-font-smoothing: antialiased; --bg-opacity: 1; background-color: #eceff1; background-color: rgba(236, 239, 241, var(--bg-opacity));">
<main>
    <div class="container col-xl-10 col-xxl-8 px-4 py-5">
        <div class="row align-items-center g-lg-5 py-5">
            <div class="col-lg-7 text-center text-lg-start">
                <div class="row text-center">
                    <div class="col">
                        <img class="d-block mx-auto mb-4" src="{{asset('images/logo-150x150.png')}}" alt="" width="100" height="100">
                    </div>
                    <div class="col mt-4">
                        <img src="{{ asset('images/pm_logo.png') }}" width="100" height="100" class="img-thumbnail" alt="perfect">
                    </div>
                </div>
                <h1 class="display-4 fw-bold lh-1 mb-3" style="color: #126e51; font-family: 'Montserrat',Arial,sans-serif;">Godwin Multiservices</h1>
                <p class="col-lg-10 fs-4" style="font-family: 'Montserrat',Arial,sans-serif;">Votre plateforme d'échange de monnaies avec une couverture sous-régionale.</p>
            </div>
            <div class="col-md-10 mx-auto col-lg-5">
                <form class="p-4 p-md-5 "  method="post" action="https://payeer.com/merchant/">
                    @csrf
                    <input type="hidden" name="m_shop" value="{{$m_shop}}">
                    <input type="hidden" name="m_orderid" value="{{$m_orderid}}">
                    <input type="hidden" name="m_amount" value="{{$m_amount}}">
                    <input type="hidden" name="m_curr" value="{{$m_curr}}">
                    <input type="hidden" name="m_desc" value="{{$m_desc}}">
                    <input type="hidden" name="m_sign" value="{{$sign}}">
                    <input type="submit" name="m_process" value="send" />
                    <input type="submit" class="w-100 btn btn-lg" style="background-color: #126e51; color: white" name="m_process" value="Continuer" />
                    <hr class="my-4">
                    <small class="text-muted"> Cliquez pour continuer la transaction</small>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>