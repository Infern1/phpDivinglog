{literal}
<script type="text/javascript">
$(document).ready(function() {
    var table = $('#{/literal}{$tablename}{literal}').DataTable({
        "order": [{/literal}{$order|default:''}{literal}],
				"pageLength": {/literal}{$num_records}{literal},
				"info":     false
    });
} );
</script>
{/literal}


