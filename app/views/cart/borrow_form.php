<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">Borrower Information</h2>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">Please fill out the form below to complete your book borrowing request.</p>

                    <form action="<?= url('index.php?action=cart-checkout') ?>" method="POST">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($_SESSION['user']['full_name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($_SESSION['user']['phone_number'] ?? '') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="return_day" class="form-label">Return Day</label>
                            <input type="date" class="form-control" id="return_day" name="return_day" required>
                            <div class="form-text">You can select a return date up to 3 weeks from today.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                             <a href="<?= url('index.php?action=cart-list') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Submit Request <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const returnDateInput = document.getElementById('return_day');

    // Set minimum date to today
    const today = new Date();
    const todayString = today.toISOString().split('T')[0];
    returnDateInput.min = todayString;

    // Set maximum date to 3 weeks from today
    const threeWeeksFromNow = new Date();
    threeWeeksFromNow.setDate(today.getDate() + 21);
    const maxDateString = threeWeeksFromNow.toISOString().split('T')[0];
    returnDateInput.max = maxDateString;

    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        alert('Request sent successfully! You will be redirected to the "My Loans" page.');
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
