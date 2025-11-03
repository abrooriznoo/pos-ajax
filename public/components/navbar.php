<div class="container-fluid">
    <a class="navbar-brand text-white" href="index.php">POS</a>
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPOS"
        aria-controls="navbarPOS" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarPOS">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link active text-white" href="?pages=product">Product</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="?pages=purchased">Purchase</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="?pages=sales">Sales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="?pages=about">About</a>
            </li>
            <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu bg-dark bg-gradient" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item text-white" href="#">Product</a></li>
                            <li><a class="dropdown-item text-white" href="#">Purchase</a></li>
                            <li><a class="dropdown-item text-white" href="#">Sales</a></li>
                            <li><a class="dropdown-item text-white" href="#">Inventory</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-white" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled text-white" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                    </li> -->
        </ul>
        <div class="d-flex align-items-center">
            <!-- <form class="d-flex">
                <input class="form-control me-2 bg-gray bg-gradient text-gray border-0" type="search"
                    placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-light" type="submit"
                    onclick="alert('Mencari kata kunci : ' + this.previousElementSibling.value)">Search</button>
            </form> -->
            <ul class="navbar-nav ms-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?php echo $_SESSION['username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end bg-dark bg-gradient" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item text-white" href="#">Profile</a></li>
                        <li><a class="dropdown-item text-white" href="#">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-white" href="auth/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>