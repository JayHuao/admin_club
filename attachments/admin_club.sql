/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : admin_club

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 04/06/2020 08:59:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity
-- ----------------------------
DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动名称',
  `club_id` int(11) NULL DEFAULT NULL COMMENT '社团id',
  `place` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动地点',
  `time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动时间',
  `population` int(11) NULL DEFAULT NULL COMMENT '人数',
  `status` tinyint(4) NULL DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity
-- ----------------------------
INSERT INTO `activity` VALUES (1, '羽毛球比赛', 1, '北面体育场', '周六日下午4点', 20, 1);

-- ----------------------------
-- Table structure for activity_apply
-- ----------------------------
DROP TABLE IF EXISTS `activity_apply`;
CREATE TABLE `activity_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NULL DEFAULT NULL COMMENT '活动id',
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '职工id',
  `apply_time` datetime(0) NULL DEFAULT NULL COMMENT '申请时间',
  `status` tinyint(4) NULL DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `deal_time` datetime(0) NULL DEFAULT NULL COMMENT '处理时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `activity_id`(`activity_id`) USING BTREE,
  INDEX `admin_id`(`admin_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity_apply
-- ----------------------------
INSERT INTO `activity_apply` VALUES (1, 1, 1, '2020-06-03 23:22:40', 3, '人数已满', '2020-06-04 08:04:57');
INSERT INTO `activity_apply` VALUES (2, 1, 2, '2020-06-04 08:09:39', 1, NULL, NULL);

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `loginfailure` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
  `logintime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `token` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态',
  `no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工号',
  `age` int(11) NULL DEFAULT NULL COMMENT '年龄',
  `sex` enum('男','女') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '性别',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES (1, 'admin', '张三', '4d1fdc9d5a7285c9a061355867fdfae1', 'ed1e90', 0, 1591232242, 1551920828, 1591232242, '4ce59e4b-25bc-47f5-a26c-b80cd7f9129c', 1, '0001', 22, '男');
INSERT INTO `admin` VALUES (2, 'user', '李四', '008104669cb8026e289c6c442d471217', 'vXY0Hk', 0, 1591229367, 1591229322, 1591232234, '', 1, '0002', 20, '男');

-- ----------------------------
-- Table structure for admin_nav
-- ----------------------------
DROP TABLE IF EXISTS `admin_nav`;
CREATE TABLE `admin_nav`  (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(8) NOT NULL,
  `nav_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单名称',
  `nav_mca` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '方法',
  `nav_ico` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '图标',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '菜单' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of admin_nav
-- ----------------------------
INSERT INTO `admin_nav` VALUES (1, 0, '控制台', 'dashboard', 'dashboard', NULL, 1);
INSERT INTO `admin_nav` VALUES (2, 0, '系统管理', 'system', 'cog', NULL, 1);
INSERT INTO `admin_nav` VALUES (3, 2, '菜单管理', 'system/menu', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (4, 0, '职工管理', 'auth', 'users', NULL, 1);
INSERT INTO `admin_nav` VALUES (5, 4, '权限管理', 'auth/rule', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (6, 4, '用户组管理', 'auth/group', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (7, 4, '职工列表', 'auth/admin', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (8, 0, '社团活动', 'community', 'tags', NULL, 1);
INSERT INTO `admin_nav` VALUES (9, 8, '职工管理', 'community/staff', '', NULL, 2);
INSERT INTO `admin_nav` VALUES (10, 8, '社团管理', 'community/club', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (11, 8, '活动管理', 'community/activity', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (12, 0, '申请管理', 'apply', 'calendar-o', NULL, 1);
INSERT INTO `admin_nav` VALUES (13, 12, '社团申请', 'apply/club', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (14, 12, '活动申请', 'apply/activity', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (15, 0, '审批管理', 'deal', 'calendar-check-o', NULL, 1);
INSERT INTO `admin_nav` VALUES (16, 15, '社团审批', 'deal/club', '', NULL, 1);
INSERT INTO `admin_nav` VALUES (17, 15, '活动审批', 'deal/activity', '', NULL, 1);

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group`  (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户组' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of auth_group
-- ----------------------------
INSERT INTO `auth_group` VALUES (1, '负责人', '1,12,4,7,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61', 1);
INSERT INTO `auth_group` VALUES (2, '职工', '1,12,42,43,44,45,46,47,48,49,50,51,52', 1);

-- ----------------------------
-- Table structure for auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `auth_group_access`;
CREATE TABLE `auth_group_access`  (
  `uid` mediumint(8) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL,
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of auth_group_access
-- ----------------------------
INSERT INTO `auth_group_access` VALUES (1, 1);
INSERT INTO `auth_group_access` VALUES (2, 2);

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule`  (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` mediumint(8) NULL DEFAULT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `condition` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 62 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限规则表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
INSERT INTO `auth_rule` VALUES (1, 0, 'dashboard', '控制台', 1, 1, '');
INSERT INTO `auth_rule` VALUES (2, 0, 'system', '系统管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (3, 2, 'system/menu', '菜单管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (4, 0, 'auth', '权限控制', 1, 1, '');
INSERT INTO `auth_rule` VALUES (5, 4, 'auth/rule', '权限管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (6, 4, 'auth/group', '用户组管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (7, 4, 'auth/admin', '管理员列表', 1, 1, '');
INSERT INTO `auth_rule` VALUES (8, 3, 'system/menu/index', '『查看』菜单', 1, 1, '');
INSERT INTO `auth_rule` VALUES (9, 3, 'system/menu/add', '『添加』菜单', 1, 1, '');
INSERT INTO `auth_rule` VALUES (10, 3, 'system/menu/edit', '『编辑』菜单', 1, 1, '');
INSERT INTO `auth_rule` VALUES (11, 3, 'system/menu/delete', '『删除』菜单', 1, 1, '');
INSERT INTO `auth_rule` VALUES (12, 1, 'dashboard/index', '『查看』控制台', 1, 1, '');
INSERT INTO `auth_rule` VALUES (13, 5, 'auth/rule/index', '『查看』权限', 1, 1, '');
INSERT INTO `auth_rule` VALUES (14, 5, 'auth/rule/add', '『添加』权限', 1, 1, '');
INSERT INTO `auth_rule` VALUES (15, 5, 'auth/rule/edit', '『编辑』权限', 1, 1, '');
INSERT INTO `auth_rule` VALUES (16, 5, 'auth/rule/delete', '『删除』权限', 1, 1, '');
INSERT INTO `auth_rule` VALUES (17, 6, 'auth/group/index', '『查看』用户组', 1, 1, '');
INSERT INTO `auth_rule` VALUES (18, 6, 'auth/group/add', '『添加』用户组', 1, 1, '');
INSERT INTO `auth_rule` VALUES (19, 6, 'auth/group/edit', '『编辑』用户组', 1, 1, '');
INSERT INTO `auth_rule` VALUES (20, 6, 'auth/group/delete', '『删除』用户组', 1, 1, '');
INSERT INTO `auth_rule` VALUES (21, 7, 'auth/admin/index', '『查看』管理员', 1, 1, '');
INSERT INTO `auth_rule` VALUES (22, 7, 'auth/admin/add', '『添加』管理员', 1, 1, '');
INSERT INTO `auth_rule` VALUES (23, 7, 'auth/admin/edit', '『编辑』管理员', 1, 1, '');
INSERT INTO `auth_rule` VALUES (24, 7, 'auth/admin/delete', '『删除』管理员', 1, 1, '');
INSERT INTO `auth_rule` VALUES (25, 7, 'auth/admin/edit_rule', '『编辑』管理员权限', 1, 1, '');
INSERT INTO `auth_rule` VALUES (26, 0, 'community', '社团活动', 1, 1, '');
INSERT INTO `auth_rule` VALUES (27, 26, 'community/staff', '职工管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (28, 27, 'community/staff/index', '『查看』职工', 1, 1, '');
INSERT INTO `auth_rule` VALUES (29, 27, 'community/staff/add', '『添加』职工', 1, 1, '');
INSERT INTO `auth_rule` VALUES (30, 27, 'community/staff/edit', '『编辑』职工', 1, 1, '');
INSERT INTO `auth_rule` VALUES (31, 27, 'community/staff/delete', '『删除』职工', 1, 1, '');
INSERT INTO `auth_rule` VALUES (32, 26, 'community/club', '社团管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (33, 32, 'community/club/index', '『查看』社团 ', 1, 1, '');
INSERT INTO `auth_rule` VALUES (34, 32, 'community/club/add', '『添加』社团', 1, 1, '');
INSERT INTO `auth_rule` VALUES (35, 32, 'community/club/edit', '『编辑』社团', 1, 1, '');
INSERT INTO `auth_rule` VALUES (36, 32, 'community/club/delete', '『删除』社团', 1, 1, '');
INSERT INTO `auth_rule` VALUES (37, 26, 'community/activity', '活动管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (38, 37, 'community/activity/index', '『查看』活动', 1, 1, '');
INSERT INTO `auth_rule` VALUES (39, 37, 'community/activity/add', '『添加』活动', 1, 1, '');
INSERT INTO `auth_rule` VALUES (40, 37, 'community/activity/edit', '『编辑』活动', 1, 1, '');
INSERT INTO `auth_rule` VALUES (41, 37, 'community/activity/delete', '『删除』活动', 1, 1, '');
INSERT INTO `auth_rule` VALUES (42, 0, 'apply', '申请管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (43, 42, 'apply/club', '社团申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (44, 43, 'apply/club/index', '『查看』社团申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (45, 43, 'apply/club/add', '『添加』社团申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (46, 43, 'apply/club/edit', '『编辑』社团申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (47, 43, 'apply/club/delete', '『删除』社团申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (48, 42, 'apply/activity', '活动申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (49, 48, 'apply/activity/index', '『查看』活动申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (50, 48, 'apply/activity/add', '『添加』活动申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (51, 48, 'apply/activity/edit', '『编辑』活动申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (52, 48, 'apply/activity/delete', '『删除』活动申请', 1, 1, '');
INSERT INTO `auth_rule` VALUES (53, 0, 'deal', '审批管理', 1, 1, '');
INSERT INTO `auth_rule` VALUES (54, 53, 'deal/club', '社团审批', 1, 1, '');
INSERT INTO `auth_rule` VALUES (55, 54, 'deal/club/index', '『查看』社团审批', 1, 1, '');
INSERT INTO `auth_rule` VALUES (56, 54, 'deal/club/pass', '『通过』社团审批', 1, 1, '');
INSERT INTO `auth_rule` VALUES (57, 54, 'deal/club/ban', '『驳回』社团审批', 1, 1, '');
INSERT INTO `auth_rule` VALUES (58, 53, 'deal/activity', '活动审批', 1, 1, '');
INSERT INTO `auth_rule` VALUES (59, 58, 'deal/activity/index', '『查看』活动审批', 1, 1, '');
INSERT INTO `auth_rule` VALUES (60, 58, 'deal/activity/pass', '『通过』活动审批', 1, 1, '');
INSERT INTO `auth_rule` VALUES (61, 58, 'deal/activity/ban', '『驳回』活动审批', 1, 1, '');

-- ----------------------------
-- Table structure for club
-- ----------------------------
DROP TABLE IF EXISTS `club`;
CREATE TABLE `club`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '编号',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `charge` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '负责人',
  `status` tinyint(4) NULL DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of club
-- ----------------------------
INSERT INTO `club` VALUES (1, '001', '体育社团', '李四', 1);

-- ----------------------------
-- Table structure for club_apply
-- ----------------------------
DROP TABLE IF EXISTS `club_apply`;
CREATE TABLE `club_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NULL DEFAULT NULL COMMENT '社团id',
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '职工id',
  `apply_time` datetime(0) NULL DEFAULT NULL COMMENT '申请时间',
  `status` tinyint(4) NULL DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `deal_time` datetime(0) NULL DEFAULT NULL COMMENT '处理时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `club_id`(`club_id`) USING BTREE,
  INDEX `admin_id`(`admin_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of club_apply
-- ----------------------------
INSERT INTO `club_apply` VALUES (1, 1, 1, '2020-06-03 23:15:09', 3, '不通过', '2020-06-04 07:55:13');
INSERT INTO `club_apply` VALUES (2, 1, 2, '2020-06-04 08:09:35', 1, NULL, NULL);

-- ----------------------------
-- Table structure for staff
-- ----------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工号',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '姓名',
  `age` int(11) NULL DEFAULT NULL COMMENT '年龄',
  `sex` enum('男','女') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '性别',
  `status` tinyint(4) NULL DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '职工表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of staff
-- ----------------------------
INSERT INTO `staff` VALUES (1, '00001', '张三', 22, '男', 1);

SET FOREIGN_KEY_CHECKS = 1;
