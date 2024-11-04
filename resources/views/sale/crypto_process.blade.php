<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <title>Payeer - Depot</title>
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
            {{-- <div class="col-lg-7 text-center text-lg-center">
                <div class="row text-center">
                    <div class="col">
                        <img class="d-block mx-auto mb-4" src="{{asset('images/logo-150x150.png')}}" alt="" width="100" height="100">
                    </div>
                    <div class="col mt-4">
                        <img src="{{ $logo }}" width="100" height="100" class="img-thumbnail" alt="{{$libelle}}">
                    </div>
                </div>
                <h1 class="display-4 fw-bold lh-1 mb-3" style="color: #126e51; font-family: 'Montserrat',Arial,sans-serif;">FINTECH GODWIN</h1>
            </div> --}}

            <div class="col-md-10 mx-auto col-lg-5 text-center">
                <div>
                    Envoyez vos <strong>{{$libelle}}</strong> sur l'adresse ci-dessous. Montant: <strong>{{$amount}} {{$currency}}</strong>
                </div>
                <div class="container mt-3" style="display: flex; justify-content: center; align-content: center">
                    <button type="button" id="copyButton" class="btn btn-outline-success">
                        <span id="address">{{\Str::substr($address, 0, 15) }} </span>
                        <button type="button" id="copyButton" class="btn btn-success">Copier addresse</button>
                    </button>
                </div>
                @if($memo)
                <div class="container mt-3" style="display: flex; justify-content: center; align-content: center">
                    <button type="button" id="copyMemo" class="btn btn-outline-success">
                        <span id="memo">{{\Str::substr($memo, 0, 15) }} </span>
                        <button type="button" id="copyMemo" class="btn btn-success">Copier memo</button>
                    </button>
                </div>
                @endif

                <div class="container mt-5" style="display: flex;  justify-content: center; align-content: center">
                    <div class="card border-success mb-0 ml-2" style="">
                    <div class="card-body text-success">
                        <h5 class="card-title">Votre code QR</h5>
                        <p class="card-text">{{$qrCodeImage}}</p>
                    </div>
                    </div>
                </div>

                <div class="container mt-5" style="display: flex;  justify-content: center; align-content: center">
                    <div class="card border-danger mb-0" style="">
                    <div class="card-body text-danger">
                        <h5 class="card-title">ATTENTION</h5>
                        <p class="card-text">Si vous ne respectez pas le réseau choisi lors de l'envoi, vous perdrez votre argent.</p>
                    </div>
                    </div>
                </div>

            </div>
        </div> 
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

<script>
    // Initialize Clipboard.js
    new ClipboardJS('#copyButton', {
        text: function() {
            return document.getElementById('address').innerText;
        }
    });
    new ClipboardJS('#copyMemo', {
        text: function() {
            return document.getElementById('memo').innerText;
        }
    });

    // Show a tooltip when the address is copied
    $('#copyButton').tooltip({
        trigger: 'click',
        placement: 'bottom'
    });

    // Hide the tooltip after copying
    $('#copyButton').on('mouseleave', function() {
        $(this).tooltip('hide');
    });

    $('#copyMemo').tooltip({
        trigger: 'click',
        placement: 'bottom'
    });

    // Hide the tooltip after copying
    $('#copyMemo').on('mouseleave', function() {
        $(this).tooltip('hide');
    });
</script>

</body>

</html>

