-- 28/9/18 
ALTER TABLE adif_proveedores.proveedor_ute_miembros ADD id_proveedor_dato_personal int DEFAULT 1 NULL;

-- 05/10/2018 Se agrega un nuevo tipo de observación
INSERT INTO `adif_proveedores`.`tipo_observacion` (`id`, `denominacion`, `fecha_creacion`, `fecha_ultima_actualizacion`, `id_tipo_timeline`) VALUES ('22', 'cae_cai', '2018-05-04 16:17:44', '2018-05-04 16:17:44', '13');

