<?php
/**
 * Created by PhpStorm.
 * User: JMaratier
 * Date: 22/10/2018
 * Time: 10:28
 */

$this->registerCSS(<<<CSS
    .circle {
    background-color: rgba(0,0,0,0);
    border: 5px solid rgba(0,183,229,0.9);
    opacity: .9;
    border-right: 5px solid rgba(0,0,0,0);
    border-left: 5px solid rgba(0,0,0,0);
    border-radius: 50px;
    box-shadow: 0 0 35px #2187e7;
    width: 50px;
    height: 50px;
    margin: 0 auto;
    -moz-animation: spinPulse 1s infinite ease-in-out;
    -webkit-animation: spinPulse 1s infinite linear;
}

.circle1 {
    background-color: rgba(0,0,0,0);
    border: 5px solid rgba(0,183,229,0.9);
    opacity: .9;
    border-left: 5px solid rgba(0,0,0,0);
    border-right: 5px solid rgba(0,0,0,0);
    border-radius: 50px;
    box-shadow: 0 0 15px #2187e7;
    width: 30px;
    height: 30px;
    margin: 0 auto;
    position: relative;
    top: -39px;
    -moz-animation: spinoffPulse 1s infinite linear;
    -webkit-animation: spinoffPulse 1s infinite linear;
}

@-moz-keyframes spinPulse {
    0% {
        -moz-transform: rotate(160deg);
        opacity: 0;
        box-shadow: 0 0 1px #2187e7;
    }

    50% {
        -moz-transform: rotate(145deg);
        opacity: 1;
    }

    100% {
        -moz-transform: rotate(-320deg);
        opacity: 0;
    }
}

@-moz-keyframes spinoffPulse {
    0% {
        -moz-transform: rotate(0deg);
    }

    100% {
        -moz-transform: rotate(360deg);
    }
}

@-webkit-keyframes spinPulse {
    0% {
        -webkit-transform: rotate(160deg);
        opacity: 0;
        box-shadow: 0 0 1px #2187e7;
    }

    50% {
        -webkit-transform: rotate(145deg);
        opacity: 1;
    }

    100% {
        -webkit-transform: rotate(-320deg);
        opacity: 0;
    }
}

@-webkit-keyframes spinoffPulse {
    0% {
        -webkit-transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
    }
}
CSS
);
 ?>

<div id="notfound">
    <div class="notfound">
        <!--<div class="circle"></div>
        <div class="circle1"></div>-->
        <div class="notfound-404">
            <h1>404</h1>
        </div>
        <h2>Oups! La page demandée n'existe pas</h2>
        <p>La page que vous recherchez a été déplacée, renommée ou est momentanément indisponible.</p>
        <p><a href="/index.php">Retour à l'accueil</a></p>
    </div>
</div>
