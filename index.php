<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>nginx traffic analyzer</title>
  <link href="https://unpkg.com/tailwindcss@next/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon">
  <style>
    td {
      word-break: break-all;
    }
    #ua tr td:first-child {
      width: 60%;
    }
    .indent {left: 1em;}
  </style>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
  <nav id="header" class="bg-white fixed w-full z-10 top-0 shadow">
    <div class="w-full container mx-auto flex flex-wrap items-center mt-0 pt-3 pb-3 md:pb-0">
      <div class="w-1/2 pl-2 md:pl-0 pb-2">
        <a class="text-gray-900 text-base xl:text-xl no-underline hover:no-underline font-bold" href="/">
          <i class="fas fa-cat text-orange-600 pr-2"></i>
          nginx traffic analyzer
        </a>
      </div>
      <div class="w-1/2 text-right pr-5"><a class="text-blue-500" href="inc/help.php"><i class="fas fa-question-circle pr-1"></i>Help</a></div>
    </div>
  </nav>

  <!--Container-->
  <div class="container w-full mx-auto pt-10">
    <div class="w-full px-4 md:px-0 md:mt-8 mb-16 text-gray-800 leading-normal">
      <div class="flex flex-row flex-wrap flex-grow mt-2">

        <div class="w-full">
          <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="path">
                Path to nginx access log(s)
              </label>
              <input style="opacity: 50%;"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="path" type="text" disabled value="<?php echo getcwd() . '/'; ?>logs" />
              <!-- @todo ^ alert if this directory is empty -->
            </div>
            <div class="flex items-center justify-start">
              <button
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mr-2 rounded focus:outline-none focus:shadow-outline"
                type="button" onclick="runReport('ip')">
                Analyze IPs
              </button>
              <button
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mr-2 rounded focus:outline-none focus:shadow-outline"
                type="button" onclick="runReport('ua')">
                Analyze User Agents
              </button>
              <button
                class="bg-blue-400 hover:bg-blue-600 text-white font-bold py-2 px-4 mr-4 rounded focus:outline-none focus:shadow-outline"
                type="button" onClick="window.location.reload();">
                Reset
              </button>
              <small>👆Eventually it'd be nice to make this user-configurable, but for now, put your logs here
                please.</small>
            </div>
          </form>
        </div>

        <!-- IP box -->
        <div class="w-full md:w-1/2">
          <div class="bg-white border rounded shadow">
            <div class="border-b p-3">
              <h5 class="font-bold uppercase text-gray-600">Top IPs</h5>
            </div>
            <div class="p-5">
              <div id="ip">
                <p>Click <em>Analyze IPs</em> to run this report.</p>
              </div>
              <div id="blockBox" class="mt-8 hidden">
                <h3 class="text-xl">Block IPs</h3>
                <p class="mt-1">Select IPs above to generate PHP code. <a class="text-blue-500" href="inc/help.php" target="_blank">What do I do with this?</a></p>
                <div class="codeblock invisible">
                  <pre
                    class="mt-3 border p-3">$deny = array(<span id="blockIPs"></span>);<br>if (in_array($_SERVER["REMOTE_ADDR"], $deny)) {<br>&nbsp;&nbsp;die("Forbidden");<br>}</pre>
                  <small class="copy cursor-pointer text-blue-500" data-clipboard-target="pre"><i class="fas fa-copy pr-2"></i>Copy to clipboard</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- UA box -->
        <div class="w-full md:w-1/2 pl-6">
          <div class="bg-white border rounded shadow">
            <div class="border-b p-3">
              <h5 class="font-bold uppercase text-gray-600">
                Top User Agents
              </h5>
            </div>
            <div class="p-5" id="ua">
              <p>Click <em>Analyze User Agents</em> to run this report.</p>
            </div>
          </div>
        </div>

      </div>
      <p style="text-align: right; font-size: small;" class="mr-5 pt-4">
        <a href="https://github.com/sarahg/nginx-traffic-analyzer">
          <i class="fab fa-github"></i>
          View source on GitHub
        </a>
      </p>
    </div>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
  <script src="js/app.js"></script>
  <script src="https://kit.fontawesome.com/a67e1f8da5.js" crossorigin="anonymous"></script>
</body>

</html>