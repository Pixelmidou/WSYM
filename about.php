<?php
/**
 * about.php - who made this: two cards on the page background, the WSYM logo on both,
 * and a collapsible list of what each person worked on.
 */
require __DIR__ . '/bootstrap.php';
page_top('WSYM Banking', ['about_us']);
?>
    <div class="container-fluid row justify-content-center align-items-center vh-100 gap-3">
        <div class="card" style="width:400px">
            <img class="card-img-top" src="<?php echo url('data/logo.png'); ?>" alt="WSYM Banking" style="width:100%;height:150px;object-fit:contain;background:#2a2a4a;padding:14px;">
            <div class="card-body">
                <h4 class="card-title">Mohamed Khabou</h4>
                <p class="card-text m-0">Participation :
                    <ul>
                        <li>Front-End Development
                            <button data-bs-toggle="collapse" data-bs-target="#mk1" style="all: unset; cursor: pointer;">&#9661;</button>
                        </li>
                        <div id="mk1" class="collapse ms-3">
                            Banking screens, the five-value buttons and the statement layout.
                        </div>
                        <li>Back-End Development
                            <button data-bs-toggle="collapse" data-bs-target="#mk2" style="all: unset; cursor: pointer;">&#9661;</button>
                        </li>
                        <div id="mk2" class="collapse ms-3">
                            Deposits, withdrawals, wires and the history reports.
                        </div>
                    </ul>
                </p>
                <a href="https://www.linkedin.com/in/mohamed-khabou-0a9654242/" target="_blank" rel="noopener" class="btn btn-primary">See Profile</a>
            </div>
        </div>
        <div class="card" style="width:400px;">
            <img class="card-img-top" src="<?php echo url('data/logo.png'); ?>" alt="WSYM Banking" style="width:100%;height:150px;object-fit:contain;background:#2a2a4a;padding:14px;">
            <div class="card-body">
                <h4 class="card-title">Youssef Guesmi</h4>
                <p class="card-text m-0">Participation :
                    <ul>
                        <li>Security Consulting
                            <button data-bs-toggle="collapse" data-bs-target="#yg1" style="all: unset; cursor: pointer;">&#9661;</button>
                        </li>
                        <div id="yg1" class="collapse ms-3">
                            Login, verification mails, password reset and the permission checks.
                        </div>
                        <li>Database
                            <button data-bs-toggle="collapse" data-bs-target="#yg2" style="all: unset; cursor: pointer;">&#9661;</button>
                        </li>
                        <div id="yg2" class="collapse ms-3">
                            The ten tables, the admin console and the audit log.
                        </div>
                    </ul>
                </p>
                <a href="" target="_blank" rel="noopener" class="btn btn-primary">See Profile</a>
            </div>
        </div>
    </div>
<?php page_bottom(); ?>
