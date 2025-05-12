document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".tab");
    const contents = document.querySelectorAll(".tab-content");

    // 現在のURLのクエリパラメータを取得
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get("tab") || "listed"; // デフォルトは 'listed'

    // 初期表示設定
    contents.forEach(content => {
        content.style.display = content.id === activeTab ? "block" : "none";
    });

    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            tabs.forEach(t => t.classList.remove("active")); // 全タブの `active` クラスを解除
            tab.classList.add("active"); // クリックされたタブに `active` クラスを追加

            contents.forEach(content => {
                content.style.display = content.id === tab.dataset.target ? "block" : "none";
            });

            history.pushState(null, "", "?tab=" + tab.dataset.target); // URLの更新（ページリロードなし）
        });
    });
});