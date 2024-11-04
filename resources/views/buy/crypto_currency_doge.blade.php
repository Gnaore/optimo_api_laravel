<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <title>Dogecoin Deposit</title>
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
            <div class="col-lg-7 text-center text-lg-center">
                <div class="row text-center">
                    <div class="col">
                        <img class="d-block mx-auto mb-4" src="{{asset('images/logo.png')}}" alt="" width="100" height="100">
                    </div>
                    <div class="col mt-2">
                        <img src="{{ asset('images/doge.png') }}" width="90" height="90" class="img-thumbnail rounded rounded-circle" alt="perfect">
                    </div>
                </div>
                <h1 class="display-4 fw-bold lh-1 mb-3" style="color: #126e51; font-family: 'Montserrat',Arial,sans-serif;">Godwin Multiservice</h1>
                <p class="col-lg fs-4 text-lg-center ml-1" style="font-family: 'Montserrat',Arial,sans-serif;">
                    Merci d'avoir choisi Godwin Multiservice, la meilleure plateforme d'échange de monnaies avec une couverture sous-régionale.
                    <br>
                    Nous apprécions avec sincérité votre marque de confiance.
                </p>
            </div>
            <div class="col-md-10 mx-auto col-lg-5 text-center">
                <form class="p-4 p-md-5 " style="font-family: 'Montserrat',Arial,sans-serif;" action="{{$url}}" method="POST">
                    @csrf
                    <small class="mb-4">Effectuez la transaction et cliquez sur le bouton confirmer</small>
                    <br>
                    <span class="btn btn-xm" onclick='copyToClipBoard("address")'><i class="far fa-copy"></i></span>
                    <div class="form-floating mb-3">
                        <input style="font-size: 14px;"  class="form-control" id='address'  disabled value="{{$address}}">
                        <label for="address">Adresse de paiement</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" disabled class="form-control" id="amount" value="{{$amount}}" >
                        <label for="amount">Montant</label>
                    </div>
                    <input class="w-100 btn btn-lg" style="background-color: #126e51; color: white"  type="submit"  name="pay" value="Confirmer" />
                    <hr class="my-4">
                    <small style="color: #126e51">Pour des raisons de sécurité, le processus de confirmation de la transaction peut prendre un certains temps sur le réseau.</small>
                    <br>
                    <small style="color: red"><strong>Payez le montant exact indiqué en DogeCoin</strong></small>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
    function copyToClipBoard (element){
        var copyText = document.getElementById(element);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
    }
</script>
</body>

</html>