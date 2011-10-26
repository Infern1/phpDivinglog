<table>
  <thead>
    <tr>
      <td>{$name}</td>
      <td>{$Country}</td>
    </tr>
  </thead>
  <tbody>
{section name=diver loop=$divers}
    <tr>
      <td><a href="{$app_path}/{$file_name}{$sep1}{$divers[diver].ID}{$list}">{$divers[diver].Firstname} {$divers[diver].Lastname}</a></td>
      <td>{$divers[diver].Country}</td>
    </tr>
{/section}
  </tbody>
</table>
