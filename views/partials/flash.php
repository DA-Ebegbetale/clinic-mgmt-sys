<?php
declare(strict_types=1);

use App\Presentation\Core\Session;

if (Session::hasFlash('success')): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars(Session::getFlash('success')); ?>
    </div>
<?php endif; ?>

<?php if (Session::hasFlash('error')): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars(Session::getFlash('error')); ?>
    </div>
<?php endif; ?>