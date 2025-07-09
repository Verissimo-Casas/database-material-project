<?php
// FILE: app/controllers/PlanoController.php

class PlanoController {
    
    public function index() {
        // Redirect to PlanoTreinoController
        redirect('plano_treino');
    }
    
    public function create() {
        // Redirect to PlanoTreinoController create
        redirect('plano_treino/create');
    }
    
    public function edit($id = null) {
        // Redirect to PlanoTreinoController edit
        redirect('plano_treino/edit' . ($id ? '/' . $id : ''));
    }
    
    public function delete($id = null) {
        // Redirect to PlanoTreinoController delete
        redirect('plano_treino/delete' . ($id ? '/' . $id : ''));
    }
}
