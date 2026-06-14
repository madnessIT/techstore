    </div><!-- /admin-content -->
</div><!-- /admin-main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar() {
    document.getElementById('adminSidebar').classList.add('show');
    document.getElementById('sidebarOverlay').classList.add('show');
}
function closeSidebar() {
    document.getElementById('adminSidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
}

// Auto-cerrar alerts
setTimeout(() => {
    document.querySelectorAll('.alert.fade.show').forEach(el => {
        const bs = bootstrap.Alert.getOrCreateInstance(el);
        bs?.close();
    });
}, 4000);

// Confirmar eliminaciones
document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', e => {
        if (!confirm(btn.dataset.confirm || '¿Confirmas esta acción?')) e.preventDefault();
    });
});

// Preview imagen
document.querySelectorAll('[data-preview]').forEach(input => {
    input.addEventListener('change', () => {
        const prev = document.getElementById(input.dataset.preview);
        if (!prev || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    });
});
</script>
<?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>
