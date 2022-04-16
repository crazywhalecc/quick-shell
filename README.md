# quick-shell

一个通过 `curl`、管道、`bash` 实现的快速执行常用命令的工具，不在本地保存脚本。

比如想随时随地不安装使用 neofetch，直接执行以下一行命令：

```bash
curl -s http://shell.zhamao.xin/run/neofetch | bash
```

## 用法

该项目由[炸毛框架](https://github.com/zhamao-robot/zhamao-framework)构建，所有数据均直接部署于炸毛所在的下载服务器上。

```bash
# 根 URI 会返回一个帮助菜单，包含了所有可以快速执行的命令
curl -s http://shell.zhamao.xin

# /run 节点可以使用管道执行一个命令，如果命令不存在，则返回一个 echo 语句，避免报错，但做到了命令不存在的提示功能
curl -s http://shell.zhamao.xin/run/{name} | bash

# 也可以不使用管道，查看快速命令名称对应要执行的 shell 代码或查看本项目的元数据（如果你不放心命令的话）
curl -s http://shell.zhamao.xin/run/neofetch
curl -s http://shell.zhamao.xin/manifest
```

## 支持的快速命令

| 名称 | 说明 |
| ---- | ---- |
| `brew-update` | 执行 Homebrew 更新（测试通断用） |
| `neofetch` | 执行 neofetch |
| ... | 持续更新中 |

## 贡献

如果你觉得有常用的命令，可以提交到这个项目中，我会尽快添加到这个项目中。

目前这个项目目前只是自己用，如果觉得不好，请提出指导性意见！
