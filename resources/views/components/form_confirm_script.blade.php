<script>
    $(function () {
        base.form.showConfirm("{{ $formId }}", "{{ $type }}", @json($ids));
    });
</script>