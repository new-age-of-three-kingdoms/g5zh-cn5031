function kakaolink_send(text, url)
{
    // 创建kakao talk链接按钮，仅在第一次启动
    Kakao.Link.sendTalkLink({
      webLink : {
        text: String(text),
        url: url // 需要输入kakaotalk app验证url
      }
    });
}