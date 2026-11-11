<table id="table1" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
    <thead>
    <tr>
        <th class='center background-th-celeste' style="visibility:collapse; display:none;">CÓDIGO</th>
        <th class='center background-th-celeste' style="visibility:collapse; display:none;">CÓDIGO EMPRESA</th>
        <th class='center background-th-celeste'>AUTOSERVICIO</th>
        <th class='center background-th-celeste'>FECHA ARCHIVO</th>
        <th class='center background-th-celeste'>NOMBRE ARCHIVO</th>
        <th class='center background-th-celeste'>CÓDIGO USUARIO CREACIÓN</th>
        <th class='center background-th-celeste'>FECHA CREACIÓN</th>
        <th class='center background-th-celeste'>CÓDIGO USUARIO MODIFICACIÓN</th>
        <th class='center background-th-celeste'>FECHA MODIFICACIÓN</th>
        <th class='center background-th-celeste' style="visibility:collapse; display:none;">CÓDIGO ESTADO</th>
    </thead>
    <tbody>
    @foreach($listaarchivo as $index=>$item)
        <tr>
            <td style="visibility:collapse; display:none;">{{$item['ID_ARCHIVO']}}</td>
            <td style="visibility:collapse; display:none;">{{$item['COD_EMPRESA']}}</td>
            <td>{{$item['AUTOSERVICIO']}}</td>
            <td>{{$item['FEC_ARCHIVO']}}</td>
            <td>{{$item['NOM_ARCHIVO']}}</td>
            <td>{{$item['USUARIO_CREA']}}</td>
            <td>{{$item['FEC_USUARIO_CREA']}}</td>
            <td>{{$item['USUARIO_MOD']}}</td>
            <td>{{$item['FEC_USUARIO_MOD']}}</td>
            <td style="visibility:collapse; display:none;">{{$item['COD_ESTADO']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(isset($ajax))
    <script type="text/javascript">
        $(document).ready(function () {
            App.dataTables();
        });
    </script>
@endif