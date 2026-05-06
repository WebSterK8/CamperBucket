<div class="card shadow-sm">

    <div class="card-header bg-alfasage text-darksage fw-bold text-center">
        Aanmelden
    </div>

    <div class="card-body">

        <!-- Foutmelding (gevuld door JS) -->
        <div id="loginError" class="alert alert-danger py-2 d-none" role="alert"></div>

        <form id="loginForm" autocomplete="off" novalidate>

            <div class="mb-3">
                <label for="username" class="form-label">Gebruikersnaam</label>
                <input type="text" class="form-control" id="username" name="username"
                       maxlength="15" required
                       pattern="[a-zA-Z0-9_\-]{5,15}"
                       title="5 tot 15 tekens: letters, cijfers, _ of -"
                       autofocus>
                <div id="usernameErr" class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
                <label for="psw" class="form-label">Wachtwoord</label>
                <input type="password" class="form-control" id="psw" name="psw"
                       minlength="8" required
                       pattern="[^\s]{8,}"
                       title="Minimaal 8 tekens, geen spaties">
                <div id="pswErr" class="invalid-feedback"></div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-outline-dark">Inloggen</button>
            </div>

        </form>

    </div>

</div>