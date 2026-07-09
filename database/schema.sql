SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` varchar(50) NOT NULL COMMENT '登录账号',
  `password` varchar(255) NOT NULL COMMENT 'bcrypt加密密码',
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `role_id` bigint unsigned NOT NULL DEFAULT 0 COMMENT '角色ID',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `last_login_ip` varchar(45) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_role_status` (`role_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='后台管理员表';

INSERT INTO `admin_user` (`username`,`password`,`real_name`,`mobile`,`role_id`,`status`) VALUES
('admin','$2y$10$9vwV1xwyNnYlD5jH7ur1R.E7QotQmqHMTmRCbMRuegt2JKn17JNmi','系统管理员','13800000000',1,1);

DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(50) NOT NULL COMMENT '角色名称',
  `role_code` varchar(50) NOT NULL COMMENT '角色编码',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '角色说明',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：1启用 0禁用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_code` (`role_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='后台角色表';

INSERT INTO `admin_role` (`id`,`role_name`,`role_code`,`description`) VALUES
(1,'超级管理员','super_admin','拥有全部菜单与接口权限'),
(2,'客服专员','service_staff','处理客户咨询与保单问答'),
(3,'保单审核员','policy_auditor','负责保单审核与状态维护');

DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `parent_id` bigint unsigned NOT NULL DEFAULT 0 COMMENT '父级菜单ID',
  `menu_name` varchar(80) NOT NULL COMMENT '菜单名称',
  `menu_type` tinyint NOT NULL DEFAULT 1 COMMENT '类型：1目录 2菜单 3按钮/接口',
  `path` varchar(160) NOT NULL DEFAULT '' COMMENT '前端路由或接口路径',
  `permission` varchar(120) NOT NULL DEFAULT '' COMMENT '权限标识',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT 'LayUI图标',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：1显示 0隐藏',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='后台菜单权限表';

INSERT INTO `admin_menu` (`id`,`parent_id`,`menu_name`,`menu_type`,`path`,`permission`,`icon`,`sort`) VALUES
(1,0,'客户保单','1','','','layui-icon-user',10),
(2,1,'客户管理','2','/admin/customer/list','customer:list','layui-icon-group',11),
(3,1,'保单管理','2','/admin/policy/list','policy:list','layui-icon-template',12),
(4,0,'AI客服','1','','','layui-icon-dialogue',20),
(5,4,'AI智能咨询','2','/admin/ai/logs','ai:list','layui-icon-service',21),
(6,0,'系统权限','1','','','layui-icon-set',30),
(7,6,'RBAC管理','2','/admin/rbac/roles','rbac:list','layui-icon-vercode',31),
(8,2,'客户新增','3','/admin/customer/save','customer:save','',0),
(9,3,'保单保存','3','/admin/policy/save','policy:save','',0),
(10,5,'AI提问','3','/admin/ai/ask','ai:ask','',0);

DROP TABLE IF EXISTS `admin_role_permission`;
CREATE TABLE `admin_role_permission` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `role_id` bigint unsigned NOT NULL COMMENT '角色ID',
  `menu_id` bigint unsigned NOT NULL COMMENT '菜单ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_menu` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色菜单权限绑定表';

INSERT INTO `admin_role_permission` (`role_id`,`menu_id`)
SELECT 1, id FROM `admin_menu`;

DROP TABLE IF EXISTS `insurance_customer`;
CREATE TABLE `insurance_customer` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '客户ID',
  `customer_no` varchar(40) NOT NULL COMMENT '客户编号',
  `name` varchar(60) NOT NULL COMMENT '客户姓名',
  `gender` tinyint NOT NULL DEFAULT 0 COMMENT '性别：0未知 1男 2女',
  `id_card` varchar(30) NOT NULL COMMENT '身份证号，明文存储页面脱敏',
  `mobile` varchar(20) NOT NULL COMMENT '手机号，明文存储页面脱敏',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `city` varchar(80) NOT NULL DEFAULT '' COMMENT '所在城市',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '联系地址',
  `risk_level` varchar(20) NOT NULL DEFAULT 'normal' COMMENT '客户风险等级：low/normal/high',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '客户备注',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_customer_no` (`customer_no`),
  UNIQUE KEY `uk_id_card` (`id_card`),
  KEY `idx_mobile` (`mobile`),
  KEY `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='保险客户表';

DROP TABLE IF EXISTS `insurance_policy`;
CREATE TABLE `insurance_policy` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '保单ID',
  `policy_no` varchar(40) NOT NULL COMMENT '保单号',
  `customer_id` bigint unsigned NOT NULL COMMENT '客户ID',
  `product_type` varchar(30) NOT NULL COMMENT '产品类型：车险/重疾险/医疗险',
  `product_name` varchar(100) NOT NULL COMMENT '保险产品名称',
  `premium_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '保费金额',
  `insured_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '保额',
  `vehicle_no` varchar(20) NOT NULL DEFAULT '' COMMENT '车牌号，车险使用',
  `effective_date` date NOT NULL COMMENT '生效日期',
  `expire_date` date NOT NULL COMMENT '到期日期',
  `status` varchar(20) NOT NULL DEFAULT '待审核' COMMENT '保单状态：待审核/生效/退保/理赔',
  `sales_channel` varchar(50) NOT NULL DEFAULT '直营网点' COMMENT '销售渠道',
  `agent_name` varchar(50) NOT NULL DEFAULT '' COMMENT '业务员姓名',
  `claim_count` int NOT NULL DEFAULT 0 COMMENT '理赔次数',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '保单备注',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_policy_no` (`policy_no`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_status_type` (`status`,`product_type`),
  KEY `idx_expire_date` (`expire_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='保险保单表';

DROP TABLE IF EXISTS `ai_chat_log`;
CREATE TABLE `ai_chat_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'AI对话ID',
  `admin_user_id` bigint unsigned NOT NULL COMMENT '后台用户ID',
  `customer_id` bigint unsigned NOT NULL DEFAULT 0 COMMENT '客户ID',
  `policy_id` bigint unsigned NOT NULL DEFAULT 0 COMMENT '保单ID',
  `question` text NOT NULL COMMENT '用户提问',
  `answer` text NOT NULL COMMENT 'AI回答',
  `model_name` varchar(60) NOT NULL COMMENT '模型名称',
  `prompt_tokens` int NOT NULL DEFAULT 0 COMMENT '输入token数量',
  `completion_tokens` int NOT NULL DEFAULT 0 COMMENT '输出token数量',
  `total_tokens` int NOT NULL DEFAULT 0 COMMENT '总token数量',
  `request_ip` varchar(45) NOT NULL DEFAULT '' COMMENT '请求IP',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：1成功 0失败/拦截',
  `error_message` varchar(500) NOT NULL DEFAULT '' COMMENT '错误信息',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_time` (`admin_user_id`,`created_at`),
  KEY `idx_customer_policy` (`customer_id`,`policy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='AI客服对话日志表';

DROP TABLE IF EXISTS `system_operate_log`;
CREATE TABLE `system_operate_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '操作日志ID',
  `admin_user_id` bigint unsigned NOT NULL DEFAULT 0 COMMENT '操作人ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '操作账号',
  `module` varchar(60) NOT NULL DEFAULT '' COMMENT '业务模块',
  `action` varchar(120) NOT NULL DEFAULT '' COMMENT '操作动作',
  `request_method` varchar(10) NOT NULL DEFAULT '' COMMENT '请求方式',
  `request_url` varchar(255) NOT NULL DEFAULT '' COMMENT '请求地址',
  `request_param` json DEFAULT NULL COMMENT '请求参数',
  `ip` varchar(45) NOT NULL DEFAULT '' COMMENT '操作IP',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '浏览器UA',
  `result_status` tinyint NOT NULL DEFAULT 1 COMMENT '结果：1成功 0失败',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_time` (`admin_user_id`,`created_at`),
  KEY `idx_module_action` (`module`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统操作日志表';

SET FOREIGN_KEY_CHECKS = 1;
