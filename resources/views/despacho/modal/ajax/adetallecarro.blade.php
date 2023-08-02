
<div class="modal-header" style = "padding: 12px !important;">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title">
    <strong>
      Carro ({{$carro->TXT_PLACA}}) <br>
      Chofer ({{$chofer->NOM_EMPR}})
    </strong>
  </h3>
</div>

<input type="hidden" name="tabestado" id='tabestado' value='prod'>

<div class="modal-body modal-pedido-poc" style = "padding: 0px !important;">

      <div class="row" style="margin-top: 35px;">
        <div class="col-sm-4 center">
          <span class="label label-primary">{{$estado->NOM_CATEGORIA}}</span>
          
        </div>
        <div class="col-sm-4 center">
          <span class="label label-warning"><=></span>
          
        </div>
        <div class="col-sm-4 center">
          <span class="label label-primary">{{$estado_envio->NOM_CATEGORIA}}</span>
          
        </div>
      </div>
  
</div>


<div class="modal-footer">
  <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancelar</button>
  <button type="submit" data-dismiss="modal" class="btn btn-success" 
  data-id='{{$carro->COD_CARRO_INGRESO_SALIDA}}'
  data-estado-id='{{$estado->COD_CATEGORIA}}'
  data-estado-cambiar-id='{{$estado_envio->COD_CATEGORIA}}'
  id="guardarcarro">Editar</button>
</div>

