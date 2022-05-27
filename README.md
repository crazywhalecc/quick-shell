# quick-shell

一个通过 `curl`、管道、`bash` 实现的快速执行常用命令的工具，不在本地硬盘保存脚本。

万能入口：

```bash
bash <(curl -s http://shell.zhamao.xin/)
```

## 用法

该项目由[炸毛框架](https://github.com/zhamao-robot/zhamao-framework)构建，所有数据均直接部署于炸毛所在的下载服务器上。

```bash
# 快速运行neofetch
bash <(curl -s http://shell.zhamao.xin/neofetch)

# 快速运行speedtest
bash <(curl -s http://shell.zhamao.xin/speedtest)

# 使用CTF工具箱（比如快速开启frp内网穿透）
# 服务端，监听0.0.0.0:7001，假设我的公网服务器IP是1.2.3.4
bash <(curl -s http://shell.zhamao.xin/ctf/frps/bind_addr=0.0.0.0/bind_port=7001)
# 客户端，穿透ssh 22端口到公网的50022端口
bash <(curl -s http://shell.zhamao.xin/ctf/frpc/1.2.3.4:7001/127.0.0.1/22/50022)
```

## 支持的快速命令

| 名称 | 说明 |
| ---- | ---- |
| `neofetch` | 在线运行 neofetch |
| `speedtest` | 在线运行 speedtest |
| `ip` | 查看本机公网 IP |
| `ctf/reverse_shell` | 使用 bash 反弹 shell，提供一个目标的 IP 和 nc 端口即可 |
| `ctf/frps` | 快速启动一个 frps 内网穿透服务器 |
| `ctf/frpc` | 快速使用 frpc 代理一个内网穿透一个端口，提供一个目标的 IP 和 TCP 端口即可 |
| ... | 持续更新中 |

## 贡献

如果你觉得有常用的命令，可以提交到这个项目中，我会尽快添加到这个项目中。

目前这个项目目前只是自己用，如果觉得不好，请提出指导性意见！
