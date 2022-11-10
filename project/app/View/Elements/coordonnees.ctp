<h2><?= __d('modecontact', 'donneesreference.titre'); ?></h2>
<table width="60%">
    <thead>
        <th width="25%"><?= __d('modecontact', 'fixe'); ?></th>
        <th width="25%"><?= __d('modecontact', 'mobile'); ?></th>
        <th width="50%"><?= __d('modecontact', 'mail'); ?></th>
    </thead>
    <tbody>
        <tr class="even">
            <td><?= $personne['numfixe']?></td>
            <td><?= $personne['numport']?></td>
            <td><?= $personne['email']?></td>
        </tr>
    </tbody>
</table>
<br><br>
<h2><?= __d('modecontact', 'donneescaf.titre'); ?></h2>
<?php if(!empty($caf)): ?>
<table width="60%">
    <thead>
        <th width="25%"><?= __d('modecontact', 'date'); ?></th>
        <th width="25%"><?= __d('modecontact', 'tel'); ?></th>
        <th width="25%"><?= __d('modecontact', 'tel2'); ?></th>
        <th width="50%"><?= __d('modecontact', 'mail'); ?></th>
    </thead>
    <tbody>
        <?php foreach($caf as $key => $c):?>
            <tr class= <?php echo ($key%2 == 0) ? "even" : "odd" ?> >
                <td><?php echo $c[0]['date'] ?></td>
                <td><?php if($c[0]['tel']) echo $c[0]['tel']?></td>
                <td><?php if($c[0]['tel2']) echo $c[0]['tel2']?></td>
                <td><?php if($c[0]['email']) echo $c[0]['email']?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p><?= __d('modecontact', 'donneescaf.aucune'); ?></p>
<?php endif; ?>
<br><br>
<h2><?= __d('modecontact', 'saisiemanuelle.titre'); ?></h2>
<ul class="actions">
    <li class="action">
        <a href=<?= "/personnes/coordonnees/".$personne['id']?> class="add"><?= __d('modecontact', 'add'); ?></a>
    </li>
</ul>
<?php if(!empty($manuel)): ?>
<table width="60%">
    <thead>
        <th width="15%"><?= __d('modecontact', 'date'); ?></th>
        <th width="25%"><?= __d('modecontact', 'tel'); ?></th>
        <th width="25%"><?= __d('modecontact', 'tel2'); ?></th>
        <th width="35%"><?= __d('modecontact', 'mail'); ?></th>
    </thead>
    <tbody>
        <?php foreach($manuel as $key => $m):?>
            <tr class= <?php echo ($key%2 == 0) ? "even" : "odd" ?> >
                <td><?php echo date_format(date_create($m[0]['modified']), 'd/m/Y')?></td>
                <td><?php if($m[0]['modified_fixe']) echo $m[0]['fixe']?></td>
                <td><?php if($m[0]['modified_mobile']) echo $m[0]['mobile']?></td>
                <td><?php if($m[0]['modified_email']) echo $m[0]['email']?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p><?= __d('modecontact', 'saisiemanuelle.aucune'); ?></p>
<?php endif; ?>




