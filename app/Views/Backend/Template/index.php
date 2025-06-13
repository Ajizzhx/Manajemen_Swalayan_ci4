<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Main Content -->
<?= $this->renderSection('content') ?>

<!-- Include JS files -->
<?= $this->include('Backend/Template/footer') ?>

<?= $this->renderSection('scripts') ?>
