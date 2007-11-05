<table>
<thead>
<tr><td>{$name}</td><td>{$Contry}</td></tr>
<tbody>
{section name=diver loop=$divers}
<tr><td><a href="{$app_path}/{$file_name}/{$divers[diver].ID}/list">{$divers[diver].Firstname} {$divers[diver].Lastname}</a> </td>
<td> {$divers[diver].Country}</td></tr>
{/section}
</table>
