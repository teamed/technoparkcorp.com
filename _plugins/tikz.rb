module Tpc
  module Blocks
    class Tikz < Liquid::Block
      def initialize(tag_name, markup, tokens)
        super
      end
      def render(context)
        site = context.registers[:site]
        name = Digest::MD5.hexdigest(super)
        if !File.exists?(File.join(site.dest, "tikz/#{name}.png"))
          temp = File.join(site.source, ".tikz-temp")
          FileUtils.mkdir_p(temp)
          File.open(File.join(temp, "#{name}.tex"), 'w') { |f|
            f.write(super)
          }
          system(
            [
              "cd .tikz-temp",
              "cat ../_latex/header.tex > doc.tex",
              "cat #{name}.tex >> doc.tex",
              "echo '\\end{document}' >> doc.tex",
              "latex -halt-on-error -interaction=nonstopmode doc.tex",
              "dvips -o doc.ps doc.dvi",
              [
                "echo quit",
                "gs -q -dNOPAUSE -sDEVICE=ppmraw -sOutputFile=- -r300 doc.ps",
                "pnmalias -bgcolor rgb:ff/ff/ff -falias -fgcolor rgb:00/00/00 -weight 0.6",
                "pnmcrop -white",
                "pnmscale 0.5",
                "pnmtopng -interlace > doc.png"
              ].join(' | '),
              "mv doc.png ../tikz/#{name}.png"
            ].join(' && ')
          )
          site.static_files << Jekyll::StaticFile.new(
            site, site.source, 'tikz', "#{name}.png"
          )
        end
        "<p><img src='/tikz/#{name}.png' alt='tikz'/></p>"
      end
    end
  end
end

Liquid::Template.register_tag('tikz', Tpc::Blocks::Tikz)
