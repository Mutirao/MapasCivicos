<?php
use MapasCulturais\i;

?>
<header id="header-inscritos" class="clearfix">
    <h3><?php i::_e("Inscritos");?></h3>
    <div class="alert info hide-tablet">
        <?php i::_e("Não é possível alterar o status das inscrições através desse dispositivo. Tente a partir de um dispositivo com tela maior.");?>
        <div class="close"></div>
    </div>
    <a class="btn btn-default download" href="<?php echo $this->controller->createUrl('report', [$entity->id]); ?>"><?php i::_e("Baixar lista de inscritos");?></a>
</header>
<div id='status-info' class="alert info">
    <p><?php i::_e("Altere os status das inscrições na última coluna da tabela de acordo com o seguinte critério:");?></p>
    <ul>
        <li><span><?php i::_e("Inválida - em desacordo com o regulamento (ex. documentação incorreta).");?></span></li>
        <li><span><?php i::_e("Pendente - ainda não avaliada.");?></span></li>
        <li><span><?php i::_e("Não selecionada - avaliada, mas não selecionada.");?></span></li>
        <li><span><?php i::_e("Suplente - avaliada, mas aguardando vaga.");?></span></li>
        <li><span><?php i::_e("Selecionada - avaliada e selecionada.");?></span></li>
        <li><span><?php i::_e("Rascunho - utilize essa opção para permitir que o responsável edite e reenvie uma inscrição. Ao selecionar esta opção, a inscrição não será mais exibida nesta tabela.");?></span></li>
    </ul>
    <div class="close"></div>
</div>

<p>
    <strong> <?php i::_e("Colunas Habilitadas:") ?> </strong><br>
    <label><input type="checkbox" ng-model="data.registrationTableColumns.number" /> <?php i::_e('Inscrição') ?> </label>
    <label><input type="checkbox" ng-model="data.registrationTableColumns.category" /> <?php i::_e('Categorias') ?> </label>
    <label><input type="checkbox" ng-model="data.registrationTableColumns.agents" /> <?php i::_e('Agentes') ?> </label>
    <label><input type="checkbox" ng-model="data.registrationTableColumns.attachments" /> <?php i::_e('Anexos') ?> </label>
    <label><input type="checkbox" ng-model="data.registrationTableColumns.evaluation" /> <?php i::_e('Avaliação') ?> </label>
    <label><input type="checkbox" ng-model="data.registrationTableColumns.status" /> <?php i::_e('Status') ?> </label>

    <label ng-repeat="field in data.opportunitySelectFields" ng-if="field.required">
        <input type="checkbox" ng-model="data.registrationTableColumns[field.fieldName]" />{{field.title}}
    </label>
</p>

<style>
    table.fullscreen {
        background-color: white;
    }
</style>
<div id="registrations-table-container">
<table id="registrations-table" class="js-registration-list registrations-table" ng-class="{'no-options': data.entity.registrationCategories.length === 0, 'no-attachments': data.entity.registrationFileConfigurations.length === 0, 'registrations-results': data.entity.published, 'fullscreen': data.fullscreenTable}"><!-- adicionar a classe registrations-results quando resultados publicados-->
    <thead>
        <tr>
            <th ng-show="data.registrationTableColumns.number" class="registration-id-col">
                <?php i::_e("Inscrição");?>
            </th>
            <th ng-show="data.registrationTableColumns.category" ng-if="data.entity.registrationCategories" class="registration-option-col" title="{{data.registrationCategory}}">
                <mc-select class="left transparent-placeholder" placeholder="status" model="registrationsFilters['category']" data="data.registrationCategoriesToFilter" title="{{data.registrationCategory}}"></mc-select>
            </th>
            <th ng-repeat="field in data.opportunitySelectFields" ng-show="data.registrationTableColumns[field.fieldName]" class="registration-option-col">
                <mc-select class="left transparent-placeholder" placeholder="{{field.title}}" model="registrationsFilters[field.fieldName]" data="field.options" title="{{field.title}}"></mc-select>
            </th>
            <th ng-show="data.registrationTableColumns.agents" class="registration-agents-col">
                <?php i::_e("Agentes");?>
            </th>
            <th ng-show="data.registrationTableColumns.attachments" ng-if="data.entity.registrationFileConfigurations.length > 0" class="registration-attachments-col">
                <?php i::_e("Anexos");?>
            </th>
            <th ng-show="data.registrationTableColumns.evaluation" class="registration-status-col">
                <?php i::_e("Avaliação");?>
            </th>
            <th ng-show="data.registrationTableColumns.status" class="registration-status-col">
                <mc-select placeholder="status" model="registrationsFilters['status']" data="data.registrationStatuses"></mc-select>
            </th>
        </tr>
    </thead>
    <tr>
        <td colspan='{{numberOfEnabledColumns()}}'>
            <label class="alignright"><input type="checkbox" class="hltip" ng-model="data.fullscreenTable"> <?php i::_e('Expandir tabela')?></label>
            
            <span ng-if="!usingRegistrationsFilters() && data.registrationsAPIMetadata.count === 0"><?php i::_e("Nenhuma inscrição.");?></span>
            <span ng-if="usingRegistrationsFilters() && data.registrationsAPIMetadata.count === 0"><?php i::_e("Nenhuma inscrição encontrada com os filtros selecionados.");?></span>
            <span ng-if="!usingRegistrationsFilters() && data.registrationsAPIMetadata.count === 1"><?php i::_e("1 inscrição.");?></span>
            <span ng-if="usingRegistrationsFilters() && data.registrationsAPIMetadata.count === 1"><?php i::_e("1 inscrição encontrada com os filtros selecionados.");?></span>
            <span ng-if="!usingRegistrationsFilters() && data.registrationsAPIMetadata.count > 1">{{data.registrationsAPIMetadata.count}} <?php i::_e("inscrições.");?>
                <?php if($entity->registrationLimit > 0):?>
                    | <?php i::_e("Número máximo de vagas na oportunidade:");?> <?php echo $entity->registrationLimit;?>
                <?php endif;?>
            </span>
            <span ng-if="usingRegistrationsFilters() && data.registrationsAPIMetadata.count > 1">{{data.registrationsAPIMetadata.count}} <?php i::_e("inscrições encontradas com os filtros selecionados.");?></span>
        </td>
    </tr>
    <tbody>
        <tr ng-repeat="reg in data.registrations" id="registration-{{reg.id}}" class="{{getStatusSlug(reg.status)}}" >
            <td ng-show="data.registrationTableColumns.number" class="registration-id-col"><a href="{{reg.singleUrl}}">{{reg.number}}</a></td>
            <td ng-show="data.registrationTableColumns.category" ng-if="data.entity.registrationCategories" class="registration-option-col">{{reg.category}}</td>
            <td ng-repeat="field in data.opportunitySelectFields" ng-if="data.registrationTableColumns[field.fieldName]" class="registration-option-col">
                {{reg[field.fieldName]}}
            </td>
            <td ng-show="data.registrationTableColumns.agents" class="registration-agents-col">
                <p>
                    <span class="label"><?php i::_e("Responsável");?></span><br />
                    <a href="{{reg.owner.singleUrl}}">{{reg.owner.name}}</a>
                </p>

                <p ng-repeat="relation in reg.agentRelations" ng-if="relation.agent">
                    <span class="label">{{relation.label}}</span><br />
                    <a href="{{relation.agent.singleUrl}}">{{relation.agent.name}}</a>
                </p>
            </td>
            <td ng-show="data.registrationTableColumns.attachments" ng-if="data.entity.registrationFileConfigurations.length > 0" class="registration-attachments-col">
                <a ng-if="reg.files.zipArchive.url" class="icon icon-download" href="{{reg.files.zipArchive.url}}"><div class="screen-reader-text"><?php i::_e("Baixar arquivos");?></div></a>
            </td>
            <td ng-show="data.registrationTableColumns.evaluation" class="registration-status-col">
                {{reg.evaluationResultString}}
            </td>
            <td ng-show="data.registrationTableColumns.status" class="registration-status-col">
                <?php if ($entity->publishedRegistrations): ?>
                    <span class="status status-{{getStatusSlug(reg.status)}}">{{getStatusNameById(reg.status)}}</span>
                <?php else: ?>
                    <mc-select model="reg" data="data.registrationStatusesNames" getter="getRegistrationStatus" setter="setRegistrationStatus"></mc-select>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan='{{numberOfEnabledColumns()}}' align="center">
                <div ng-if="data.findingRegistrations">
                    <img src="<?php $this->asset('img/spinner_192.gif')?>" width="48">
                </div>
            </td>
        </tr>
    </tfoot>
</table>
</div>