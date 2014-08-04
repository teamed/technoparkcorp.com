module Tpc
  module Tags
    class Steps < Liquid::Tag
      def render(context)
        page = context['page']
        if page['next_step'].nil?
          nxt = 'process/scope'
        else
          nxt = page['next_step'].strip
        end
        ul(nxt, context, 0)
      end

      def ul(name, context, total)
        return '' unless total < 4
        post = find(name, context)
        html = li(post)
        nxt = post['next_step']
        if nxt
          html += ul(nxt, context, total + 1)
        end
        html
      end

      def find(name, context)
        site = context.registers[:site]
        site.posts.each do |post|
          if post.permalink == name
            return post
          end
        end
        raise "can't find article '#{name}'"
      end

      def li(post)
        "<li><a href='/#{post.permalink}'>#{post['intro']}</a></li>"
      end
    end
  end
end

Liquid::Template.register_tag('steps', Tpc::Tags::Steps)
