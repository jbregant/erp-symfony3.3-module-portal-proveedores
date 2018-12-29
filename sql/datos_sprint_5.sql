-- 29-10: se agregan los tipos de actividad para compatibilidad con siga
INSERT INTO adif_compras.tipo_actividad (denominacion, descripcion, fecha_creacion, fecha_ultima_actualizacion, fecha_baja, id_usuario_creacion, id_usuario_ultima_modificacion) VALUES ('990001 - Prestacion de Servicios', 'Prestacion de Servicios', '2018-10-29 10:33:17', '2018-10-29 10:33:17', null, null, null);
INSERT INTO adif_compras.tipo_actividad (denominacion, descripcion, fecha_creacion, fecha_ultima_actualizacion, fecha_baja, id_usuario_creacion, id_usuario_ultima_modificacion) VALUES ('990002 - Exportacion de Bienes', 'Exportacion de Bienes', '2018-10-29 10:33:17', '2018-10-29 10:33:17', null, null, null);

-- 09-11: feature/pp-501
-- Los tipos de documentos tienen que respetar esos ID  o adecuar las constantes en el archivo src/GYL/ProveedorBundle/Entity/ProveedorDatoPersonal.php
INSERT INTO adif_rrhh.tipo_documento (nombre) VALUES ('DNI');
INSERT INTO adif_rrhh.tipo_documento (nombre) VALUES ('EXTRANJERO');