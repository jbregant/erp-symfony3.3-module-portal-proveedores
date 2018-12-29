-- 19/09 Pre Inscripción multiple
ALTER TABLE adif_proveedores.proveedor_actividad ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_dato_bancario ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_dato_contacto ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_dato_gcshm ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_dato_impositivo ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_declaracion_jurada ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_documentacion ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_domicilio ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_evaluacion ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_representante_apoderado ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_rubro ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_timeline ADD id_proveedor_dato_personal int DEFAULT 1 NULL;
ALTER TABLE adif_proveedores.proveedor_ute ADD id_proveedor_dato_personal int DEFAULT 1 NULL;

-- 19/09 Relación preinscripción - proveedor
ALTER TABLE adif_proveedores.proveedor_dato_personal 
ADD proveedor_id INT,
ADD FOREIGN KEY (proveedor_id) REFERENCES adif_compras.proveedor(id);
